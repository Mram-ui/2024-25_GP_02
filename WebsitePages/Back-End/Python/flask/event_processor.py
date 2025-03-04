import threading
import time
from datetime import datetime
import datetime
import time
import cv2
import mysql.connector
from ultralytics import YOLO
from apscheduler.schedulers.background import BackgroundScheduler
import atexit
from flask import Flask
import os

import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from tensorflow.keras.applications import ResNet50
from tensorflow.keras.applications.resnet50 import preprocess_input
from tensorflow.keras.preprocessing import image

# YOLO model path
MODEL_PATH = '../../Yolo/best.pt'
MODEL_PATH = '../../../../Yolo/best.pt'

model = YOLO(MODEL_PATH)
gender_model=YOLO('../../../../Yolo/gender_classification.pt')  # Using YOLOv8 nano for classification

# Global dictionary to track merged IDs
id_mapping = {}
# Global dictionary to track EnteranceTime and ExitTime for each person in each session
person_tracking = {}  # Format: {person_id: {"session_id": {"EnteranceTime": timestamp, "ExitTime": timestamp}}}

latest_session_id={} # key: hall_id, value: session_id



# Initialize the YOLO model
model = YOLO(MODEL_PATH)

# List to temporarily store people count data for each camera
camera_data = {} #key: session_id, value: people_count, timestamp, hall_id


# Define database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'root',
    'database': 'raqeebdb'
}

# Function to establish a database connection
def get_db_connection():
    connection = mysql.connector.connect(
        host=db_config['host'],
        user=db_config['user'],
        password=db_config['password'],
        database=db_config['database']
    )
    return connection

def retrieve_halls(event_id, cursor):
    cursor.execute(f'SELECT * FROM hall WHERE EventID={event_id}')
    halls_data = cursor.fetchall()

    # returns list of halls
    return halls_data

def retrieve_cameras(camera_id, cursor):
    cursor.execute(f'SELECT * FROM camera WHERE CameraID={camera_id}')
    cameras_data = cursor.fetchall()

    # returns list of cameras
    return cameras_data

def retrieve_events(event_id, cursor):

    cursor.execute(f'SELECT * FROM events WHERE EventID={event_id}')
    events_data = cursor.fetchall()

    # returns event data
    return events_data


# Retrieve all current events in the db
def retrieve_current_events():

    connection = get_db_connection()
    try:
        cursor = connection.cursor(dictionary=True)

        # Define the current date and time
        now = datetime.datetime.now()

        # Query to fetch current events
        query = '''
            SELECT * 
            FROM events
            WHERE 
                (EventStartDate < %s OR (EventStartDate = %s AND EventStartTime <= %s))
                AND 
                (EventEndDate > %s OR (EventEndDate = %s AND EventEndTime >= %s))
        '''

        # Execute the query with the current date and time
        cursor.execute(query, (now.date(), now.date(), now.time(), now.date(), now.date(), now.time()))

        # Fetch all current events
        current_events = cursor.fetchall()

        # Print and return the events
        for event in current_events:
            cursor.execute('SELECT HallID FROM hall WHERE EventID=%s', (event['EventID'],))
            halls = cursor.fetchall()
            event['Halls'] = halls  # Add halls as a new key to the event

        # print('return current_events')
        return current_events
    finally:
        # Ensure resources are closed properly
        cursor.close()
        connection.close()
# Retrieve camera details of current events
def retrieve_camera_details():
    connection = get_db_connection()
    
    try:
        cursor = connection.cursor(dictionary=True)
        all_current_events = retrieve_current_events()
        # List containing all data for each camera in an event
        detailed_camera_data = []

        for event in all_current_events:
            # Retrieve camera information
            halls_data = retrieve_halls(event['EventID'], cursor)

            for hall in halls_data:
                camera_id = hall['CameraID']
                hall_id = hall['HallID'] # this is the key in latest_session_id, use it directly, it's already inside a loop
                numOfHalls = len(halls_data) # Count the number of halls
                # Retrieve all information for each CameraID
                cameras_data = retrieve_cameras(camera_id, cursor)
                # print("Latest Session ID Dictionary:", latest_session_id)

                # print('-------------------------------',latest_session_id[hall_id],'-----------------------------')
                for camera in cameras_data:
                    rtsp_link = f"rtsp://{camera['CameraUsername']}:{camera['CameraPassword']}@{camera['CameraIPAddress']}:{camera['PortNo']}/{camera['StreamingChannel']}"
                        # 'rtsp://cameraUserName:cameraPassword@CameraIPAddress:PortNo/StreamingChannel'
                    detailed_camera_data.append({
                        'HallID': hall_id,
                        'HallName': hall['HallName'],
                        'cameraName': camera['CameraName'],
                        'CameraID': camera_id,
                        'rtsp_link': rtsp_link,
                        #  'SessionID': session_id, from gList>>>> latest_session_id[hall_id]
                        'SessionID': latest_session_id[hall_id],
                        'event_id': event['EventID'],
                    })

        return detailed_camera_data

    finally:
        # Ensure resources are closed properly
        cursor.close()
        connection.close()
    
#---------------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------------



def generate_frames(rtsp_link, session_id, hall_id, event_id):
    '''
    This function initiates the camera connection for a given camera.
    It is called by the `start_frame_reading()` function using threading, 
    allowing simultaneous processing for multiple cameras.

    1- connect to the camera using its RTSP link
    2- calculate how many frames to skip so that a frame is processed approximately every 5 seconds
    3- start reading the camera's stream continuously
    4- Every 5 seconds, send a frame to the `frame_handler` function for further processing (counting people in the frame).
    '''
    try: 
        cap = cv2.VideoCapture(rtsp_link) # Connect to the camera
        if not cap.isOpened():
            print(f"Failed to open RTSP link: {rtsp_link}")
            return
        fps = cap.get(cv2.CAP_PROP_FPS)  # Get frames per second of the video source
        # frames_to_skip = int(fps * 5)  # Calculate how many frames to skip for 5 seconds
        frames_to_skip = int(fps * 2)  # Calculate how many frames to skip for 5 seconds
        frame_count = 0  # Counter for frames
        while True:
            success, frame = cap.read() # Read a frame from the video stream.
            if not success:
                break

            cv2.imshow("Stream", frame)
            if cv2.waitKey(1) & 0xFF == ord('q'):
                break
                    
            # Process and send a frame approximately every 5 seconds based on the calculated interval
            if frame_count % frames_to_skip == 0:
                # Use threading to send the frame to the `frame_handler` to avoid blocking the stream reading
                threading.Thread(target=frame_handler, args=(frame.copy(), latest_session_id[hall_id], hall_id, event_id)).start()

            frame_count += 1  # Increment the frame count

    except Exception as e:
        print(f"Exception occurred in generate_frames: {e}")



#This function initiates reading for all active cameras
def start_frame_reading():
        all_cameras = retrieve_camera_details()  # Retrieve all cameras
        
        for camera in all_cameras:
            hall_id = camera['HallID']
            session_id = camera['SessionID']
            event_id = camera['event_id']
            if session_id:
                threading.Thread(target=generate_frames, args=(camera['rtsp_link'], session_id, hall_id, event_id)).start()
                #print(f"Started reading frames for CameraID: {camera['CameraID']}, SessionID: {session_id}, in HallID: {hall_id}")



    
def scheduler():
    sched = BackgroundScheduler(daemon=True)
    sched.add_job(session_scheduler,'interval',hours=24, next_run_time=datetime.datetime.now())
    sched.start()
    print("Scheduler started. Latest Session ID updated:", latest_session_id)
    # Shut down the scheduler when exiting the app
    atexit.register(lambda: sched.shutdown())

def session_scheduler():
    """ Function for test purposes. """

    #print("Scheduler is alive! ... ", datetime.now())

    try:
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        
        # Call current_events to retrieve: event_id and hall data for the event
        current_events = retrieve_current_events()
        
        temp_session_id = {}
        # Loop through all events and their halls
        for event in current_events:
            for hall in event['Halls']:  # Access each hall in the event
                
                # Insert a new session entry into MonitoredSession
                cursor.execute("""
                    INSERT INTO MonitoredSession (HallID) VALUES (%s)
                """, (hall['HallID'],))
                
                # Commit the transaction
                connection.commit()

                # Get the last inserted session ID
                session_id = cursor.lastrowid
                
                # Update latest_session_id_list only if insertion was successful
                if session_id:
                    #print(f"Successfully inserted HallID {hall['HallID']} with SessionID {session_id}")

                    # Add the newly inserted session ID to temp_session_id
                    temp_session_id[hall['HallID']] = session_id
                else:
                    print(f"Failed to insert HallID {hall_id}")
        
        # Update latest_session_id_list 
        latest_session_id.update(temp_session_id)
        # Print the updated latest_session_id
        print("Updated latest_session_id:", latest_session_id)
        
    finally:
        # Ensure resources are properly closed
        cursor.close()
        connection.close()

#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------------------------------------------
def save_to_database():
    """
    Periodically save data to MySQL every 5 seconds for all the active cameras.
    Saves people count data to the `peoplecount` table and tracking data to the `PersonTrack` table.
    """
    while True:
        time.sleep(3)  # Pause the loop for 3 seconds before the next save attempt
        db_connection = get_db_connection()  # Establish a new database connection
        cursor = db_connection.cursor()  # Create a cursor object for executing queries
        all_data_saved = True  # Flag to track if all insertions were successful

        # Iterate over each session in the camera_data dictionary
        for session, data in camera_data.items():
            if data:  # Only proceed if there's data for this session
                # Save people count data to the `peoplecount` table
                data_to_save = [(entry[0], entry[1], entry[2]) for entry in data]  # Save (Count, Time, SessionID)
                query = "INSERT INTO peoplecount (Count, Time, SessionID) VALUES (%s, %s, %s)"
                try:
                    cursor.executemany(query, data_to_save)
                    # Only commit if insertion was successful
                    db_connection.commit()
                    camera_data[session].clear()  # Clear data for this session after saving
                except mysql.connector.Error as err:
                    all_data_saved = False
                    print(f"Error saving data for session {session}: {err}")
                    db_connection.rollback()

        # Save tracking data to the `PersonTrack` table
        # Create a list of keys to avoid modifying the dictionary during iteration
        person_ids = list(person_tracking.keys())
        for person_id in person_ids:
            sessions = person_tracking[person_id]
            # Create a list of session IDs to avoid modifying the dictionary during iteration
            session_ids = list(sessions.keys())
            for session_id in session_ids:
                tracking_data = sessions[session_id]
                if tracking_data["ExitTime"] is not None:  # Only save if ExitTime is set
                    # Check if the detection count meets the minimum threshold (e.g., 3 frames)
                    if tracking_data["DetectionCount"] >= 3:
                        query = """
                            INSERT INTO PersonTrack (ID, EntranceTime, ExitTime, SessionID, Gender)
                            VALUES (%s, %s, %s, %s)
                        """
                        try:
                            # Convert timestamps to the correct format
                            entrance_time = datetime.datetime.strptime(tracking_data["EnteranceTime"], "%Y%m%d_%H%M%S").strftime("%Y-%m-%d %H:%M:%S")
                            exit_time = datetime.datetime.strptime(tracking_data["ExitTime"], "%Y%m%d_%H%M%S").strftime("%Y-%m-%d %H:%M:%S")

                            # Debug log: Print the data being inserted
                            print(f"Inserting tracking data: ID={person_id}, EntranceTime={entrance_time}, ExitTime={exit_time}, SessionID={session_id}")

                            gender=tracking_data["Gender"]


                            # Execute the query
                            cursor.execute(query, (person_id, entrance_time, exit_time, session_id, gender))
                            db_connection.commit()
                            print("##################################TRACKER_SAVED_INTO_DB#####################")
                        except mysql.connector.Error as err:
                            all_data_saved = False
                            print(f"Error saving tracking data for person {person_id} in session {session_id}: {err}")
                            db_connection.rollback()
                        except Exception as e:
                            all_data_saved = False
                            print(f"Unexpected error saving tracking data for person {person_id} in session {session_id}: {e}")
                            db_connection.rollback()

                    # Remove the saved tracking data to avoid duplicate entries
                    del person_tracking[person_id][session_id]
                    if not person_tracking[person_id]:  # If no more sessions for this person, remove the person
                        del person_tracking[person_id]
                else:
                    # Discard the person ID if the detection count is below the threshold
                    if tracking_data["DetectionCount"] < 3:
                        print(f"Discarding person {person_id} in session {session_id} (detected only {tracking_data['DetectionCount']} times)")
                        del person_tracking[person_id][session_id]
                        if not person_tracking[person_id]:  # If no more sessions for this person, remove the person
                            del person_tracking[person_id]

        # Print success message only if all insertions were successful
        if all_data_saved:
            print("All data successfully saved and cleared for all sessions.")
        else:
            print("Some data failed to save.")

        cursor.close()
        db_connection.close()


def ensure_directory_exists(path):
    """
    Ensure the given directory path exists. Create it if it does not.
    """
    if not os.path.exists(path):
        os.makedirs(path)


##############--------------------------------#################################################


# Load a pre-trained ResNet50 model for feature extraction
feature_extraction_model = ResNet50(weights='imagenet', include_top=False, pooling='avg')

def extract_features(img):
    """
    Extract features (embeddings) from a cropped image using a pre-trained model.
    """
    img = cv2.resize(img, (224, 224))  # Resize image to the input size of ResNet50
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = preprocess_input(img_array)
    features = feature_extraction_model.predict(img_array)
    return features.flatten()

def load_embeddings(event_id, session_id):
    """
    Load all saved embeddings for a given event and session.
    Directory structure: detection_embeddings/event_id/person_id/session_id/timestamp.npy
    """
    base_dir = "detection_embeddings"
    embeddings = {}
    event_dir = os.path.join(base_dir, str(event_id))
    
    if os.path.exists(event_dir):
        for person_id in os.listdir(event_dir):
            person_dir = os.path.join(event_dir, person_id)
            # session_dir = os.path.join(person_dir, str(session_id))
            if os.path.exists(person_dir):
                for embedding_file in os.listdir(person_dir):
                    embedding_path = os.path.join(person_dir, embedding_file)
                    embedding = np.load(embedding_path)
                    embeddings[person_id] = embedding
    return embeddings

def compare_embeddings(new_embedding, saved_embeddings, threshold=0.7):
    """
    Compare a new embedding with saved embeddings to find a match.
    If similarity is above the threshold, merge the IDs.
    """
    global id_mapping
    for person_id, saved_embedding in saved_embeddings.items():
        similarity = cosine_similarity([new_embedding], [saved_embedding])[0][0]
        if similarity > threshold:
            # Merge IDs if they are not already mapped
            if person_id in id_mapping:
                return id_mapping[person_id]  # Return the existing mapped ID
            else:
                return person_id  # Return the matched person ID
    return None

def save_cropped_image(crop, event_id, person_id, session_id, enterance_time, exit_time, counter):
    """
    Save a cropped image in the appropriate directory structure.
    Directory structure: detection_imgs/event_id/person_id/
    File name format: person_id, Session_id, EnteranceTime, ExitTime, Counter.jpg
    """
    # Base directory to store images
    base_dir = "detection_imgs"

    # Directory path: detection_imgs/event_id/person_id/
    dir_path = os.path.join(base_dir, str(event_id), str(person_id))
    ensure_directory_exists(dir_path)
    
    # Filename: person_id, Session_id, EnteranceTime, ExitTime, Counter.jpg
    filename = f"{person_id}, {session_id}, {enterance_time}, {exit_time}, {counter}.jpg"
    file_path = os.path.join(dir_path, filename)
    
    # Debugging log
    print(f"Saving cropped image to: {file_path}")
    cv2.imwrite(file_path, crop)

def save_embedding(embedding, event_id, person_id, session_id, enterance_time, exit_time, counter):
    """
    Save the embedding of a detected person in the appropriate directory structure.
    Directory structure: detection_embeddings/event_id/person_id/
    File name format: person_id, Session_id, EnteranceTime, ExitTime, Counter.npy
    """
    base_dir = "detection_embeddings"
    dir_path = os.path.join(base_dir, str(event_id), str(person_id))
    ensure_directory_exists(dir_path)
    
    # Filename: person_id, Session_id, EnteranceTime, ExitTime, Counter.npy
    filename = f"{person_id}, {session_id}, {enterance_time}, {exit_time}, {counter}.npy"
    file_path = os.path.join(dir_path, filename)
    np.save(file_path, embedding)

def rename_last_file(event_id, person_id, session_id, enterance_time, exit_time, counter):
    """
    Rename the last saved file to include the ExitTime.
    """
    base_dir = "detection_imgs"
    dir_path = os.path.join(base_dir, str(event_id), str(person_id))

    # Find the last file with ExitTime=None
    for filename in os.listdir(dir_path):
        if filename.endswith(f"{enterance_time}, None, {counter}.jpg"):
            old_file_path = os.path.join(dir_path, filename)
            new_filename = f"{person_id}, {session_id}, {enterance_time}, {exit_time}, {counter}.jpg"
            new_file_path = os.path.join(dir_path, new_filename)
            os.rename(old_file_path, new_file_path)
            print(f"Renamed file to: {new_file_path}")
            break

# def frame_handler(frame, session_id, hall_id, event_id):
#     """
#     Process a frame to detect, count people, save cropped images, and merge IDs.
#     """
#     global id_mapping, person_tracking
#     results = model.track(frame, conf=0.5, classes=0, persist=True, tracker="botsort.yaml")
#     people_count = 0  # Initialize people count

#     # Load saved embeddings for the current event and session
#     saved_embeddings = load_embeddings(event_id, session_id)

#     # Track currently detected persons in this frame
#     current_detections = set()

#     # Process detections to count people and save cropped images
#     for result in results:
#         for box in result.boxes:
#             if int(box.cls) == 0:  # 'person' class detected (class ID 0: person)
#                 people_count += 1
#                 if box.id is not None:
#                     # Extract bounding box coordinates
#                     x1, y1, x2, y2 = map(int, box.xyxy[0])
#                     person_id = str(int(box.id.item()))  # Convert tensor to int, then to string
#                     current_detections.add(person_id)  # Add to current detections

#                     # Crop the detected person from the frame
#                     crop = frame[y1:y2, x1:x2]
                    
#                     # Capture timestamp for image naming
#                     timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
                    
#                     # Extract features (embeddings) from the cropped image
#                     new_embedding = extract_features(crop)

#                     # Compare the new embedding with saved embeddings
#                     matched_person_id = compare_embeddings(new_embedding, saved_embeddings)

#                     if matched_person_id:
#                         # Merge IDs
#                         print(f"Person {person_id} matched with previously detected person {matched_person_id}")
#                         id_mapping[person_id] = matched_person_id  # Update the global ID mapping
#                         merged_id = matched_person_id
#                     else:
#                         merged_id = person_id
#                         gender_results=gender_model.predict(crop)
#                         pred_class = gender_results[0].probs.top1  # returns class 0 or 1
#                         gender = "male" if pred_class == 1 else "female"  # get gender based on class index

#                     # Update tracking for the person
#                     if merged_id not in person_tracking:
#                         person_tracking[merged_id] = {}
#                     if session_id not in person_tracking[merged_id]:
#                         # New entrance: Initialize tracking data
#                         person_tracking[merged_id][session_id] = {
#                             "EnteranceTime": timestamp,
#                             "LastDetectionTime": timestamp,
#                             "ExitTime": None,
#                             "Counter": 0,
#                             "DetectionCount": 0,  # Initialize detection count
#                             "Gender": gender                         }
#                         print(f"New person detected: {merged_id}")

#                     # Increment the detection count and counter
#                     person_tracking[merged_id][session_id]["DetectionCount"] += 1
#                     person_tracking[merged_id][session_id]["Counter"] += 1
#                     counter = person_tracking[merged_id][session_id]["Counter"]

#                     # Update the last detection time
#                     person_tracking[merged_id][session_id]["LastDetectionTime"] = timestamp

#                     # Save the cropped image and embedding
#                     save_cropped_image(crop, event_id, merged_id, session_id, person_tracking[merged_id][session_id]["EnteranceTime"], person_tracking[merged_id][session_id]["ExitTime"], counter)
#                     save_embedding(new_embedding, event_id, merged_id, session_id, person_tracking[merged_id][session_id]["EnteranceTime"], person_tracking[merged_id][session_id]["ExitTime"], counter)

#                     # Draw bounding box with the merged ID
#                     cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
#                     cv2.putText(frame, f"ID: {merged_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

#     # Update ExitTime for persons no longer detected
#     for person_id in list(person_tracking.keys()):
#         if person_id not in current_detections and session_id in person_tracking[person_id]:
#             if person_tracking[person_id][session_id]["ExitTime"] is None:
#                 # Check if the person has not been detected for a while (e.g., 5 seconds)
#                 last_detection_time = datetime.datetime.strptime(person_tracking[person_id][session_id]["LastDetectionTime"], "%Y%m%d_%H%M%S")
#                 current_time = datetime.datetime.now()
#                 if (current_time - last_detection_time).total_seconds() > 5:  # 5 seconds threshold
#                     person_tracking[person_id][session_id]["ExitTime"] = person_tracking[person_id][session_id]["LastDetectionTime"]
#                     print(f"Person {person_id} exited at {person_tracking[person_id][session_id]['ExitTime']}")

#                     # Rename the last saved image and embedding to include the ExitTime
#                     rename_last_file(event_id, person_id, session_id, person_tracking[person_id][session_id]["EnteranceTime"], person_tracking[person_id][session_id]["ExitTime"], person_tracking[person_id][session_id]["Counter"])
#         elif person_id in current_detections and session_id in person_tracking[person_id]:
#             # Update the last detection time
#             person_tracking[person_id][session_id]["LastDetectionTime"] = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")

#     # Capture the current timestamp for the frame processing
#     log_timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

#     # Initialize the session_id key in the `camera_data` dictionary if not already present
#     if session_id not in camera_data:
#         camera_data[session_id] = []

#     # Append the processed data to the session's list
#     camera_data[session_id].append((people_count, log_timestamp, session_id, hall_id))

#     print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")

def frame_handler(frame, session_id, hall_id, event_id):
    """
    Process a frame to detect and count people, draw bounding boxes, and log results

    Parameters:
    - frame: The image frame to process (received from the camera stream).
    - session_id: The unique identifier for the current session.
    - hall_id: The identifier for the hall associated with the camera.

    Steps:
    1. Receives a frame every 5 seconds from the `generate_frames()` function.
    2. Counts the number of people detected in the received frame using the `model.track()` method.
    3. Saves the timestamp of the detection for future analysis.
    4. Logs the results (people_count, timestamp, session_id, hall_id) in the `camera_data` dictionary for database storage.

    """
    # Use the model to detect objects and track them
    results = model.track(frame, conf=0.3, classes=0, persist=True, tracker="botsort.yaml")
    people_count = 0 # Initialize people count

    # Process detections to count people
    for result in results:
        for box in result.boxes:
            if int(box.cls) == 0:  # 'person' class detected (class ID 0: person, class ID 1: head)
                people_count += 1
                if box.id is not None:
                    # Extract bounding box coordinates
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    # Draw bounding box
                    cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                    person_id = box.id[0] # Add a label with the person's ID
                    cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)


    # Capture the current timestamp for the frame processing
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    # Initialize the session_id key in the `camera_data` dictionary if not already present
    if session_id not in camera_data:
            camera_data[session_id] = []

    # Append the processed data to the session's list
    camera_data[session_id].append((people_count, timestamp, session_id, hall_id))

    print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")


if __name__ == '__main__':

    scheduler()
    time.sleep(0.5)
    start_frame_reading()
    db_thread = threading.Thread(target=save_to_database, daemon=True).start()

    # Keep the main thread alive to allow the scheduler to run
    try:
        print("Application is running. Press Ctrl+C to exit.")
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Application is shutting down.")
    


