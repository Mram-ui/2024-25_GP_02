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
# YOLO model path
MODEL_PATH = '../../Yolo/best.pt'
MODEL_PATH = '../../../../Yolo/best.pt'

model = YOLO(MODEL_PATH)


latest_session_id={} # key: hall_id, value: session_id

# Initialize shared camera_data
#camera_data = get_shared_camera_data()

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
        frames_to_skip = int(fps * 3)  # Calculate how many frames to skip for 5 seconds
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
    Periodically save data to MySQL every 5 seconds for all the active cameras
    """
    while True:
        # time.sleep(5)  # Pause the loop for 5 seconds before the next save attempt
        time.sleep(3)  # Pause the loop for 5 seconds before the next save attempt
        db_connection = get_db_connection() #  Establish a new database connection
        cursor = db_connection.cursor() # Create a cursor object for executing queries
        all_data_saved = True  # Flag to track if all insertions were successful

        # Iterate over each session in the camera_data dictionary
        for session, data in camera_data.items():
            if data:  # Only proceed if there's data for this session
                data_to_save = [(entry[0], entry[1], entry[2]) for entry in data] # Save (Count, Time, SessionID)
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


def save_cropped_image(crop, event_id, session_id, person_id, timestamp):
    """
    Save a cropped image in the appropriate directory structure.

    Parameters:
    - crop: The cropped image (numpy array).
    - event_id: The event ID.
    - session_id: The session ID.
    - person_id: The ID of the detected person.
    - timestamp: The timestamp for the detection (used in the image filename).
    """
    # Base directory to store images
    base_dir = "detection_imgs"
    
    # Directory path: detections/event_id/session_id/person_id/
    dir_path = os.path.join(base_dir, str(event_id), str(session_id), str(person_id))
    ensure_directory_exists(dir_path)
    
    # Filename: timestamp.jpg
    filename = f"{timestamp}.jpg"
    file_path = os.path.join(dir_path, filename)
    
    # Save the cropped image
    cv2.imwrite(file_path, crop)


def frame_handler(frame, session_id, hall_id, event_id):
    """
    Process a frame to detect and count people, save cropped images, and log results.

    Updated Steps:for visualization
    1. Detect people and count them.
    2. Crop and save detected person images in the directory structure.
    3. Log results (people_count, timestamp, session_id, hall_id).
    """
    # Use the model to detect objects and track them
    results = model.track(frame, conf=0.3, classes=0, persist=True, tracker="botsort.yaml")
    people_count = 0  # Initialize people count
    # event_id = None  # Retrieve the event_id dynamically if available

    # Process detections to count people and save cropped images
    for result in results:
        for box in result.boxes:
            if int(box.cls) == 0:  # 'person' class detected (class ID 0: person)
                people_count += 1
                if box.id is not None:
                    # Extract bounding box coordinates
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    person_id = box.id[0]  # Track ID of the person
                    
                    # Crop the detected person from the frame
                    crop = frame[y1:y2, x1:x2]
                    
                    # Capture timestamp for image naming
                    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S_%f")
                    
                    # Save the cropped image
                    save_cropped_image(crop, event_id, session_id, person_id, timestamp)

                    # Draw bounding box  (optional)
                    cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                    cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

    # Capture the current timestamp for the frame processing
    log_timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    # Initialize the session_id key in the `camera_data` dictionary if not already present
    if session_id not in camera_data:
        camera_data[session_id] = []

    # Append the processed data to the session's list
    camera_data[session_id].append((people_count, log_timestamp, session_id, hall_id))

    print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")




# def frame_handler(frame, session_id, hall_id, event_id):
#     """
#     Process a frame to detect and count people, draw bounding boxes, and log results

#     Parameters:
#     - frame: The image frame to process (received from the camera stream).
#     - session_id: The unique identifier for the current session.
#     - hall_id: The identifier for the hall associated with the camera.

#     Steps:
#     1. Receives a frame every 5 seconds from the `generate_frames()` function.
#     2. Counts the number of people detected in the received frame using the `model.track()` method.
#     3. Saves the timestamp of the detection for future analysis.
#     4. Logs the results (people_count, timestamp, session_id, hall_id) in the `camera_data` dictionary for database storage.

#     """
#     # Use the model to detect objects and track them
#     results = model.track(frame, conf=0.3, classes=0, persist=True, tracker="botsort.yaml")
#     people_count = 0 # Initialize people count

#     # Process detections to count people
#     for result in results:
#         for box in result.boxes:
#             if int(box.cls) == 0:  # 'person' class detected (class ID 0: person, class ID 1: head)
#                 people_count += 1
#                 if box.id is not None:
#                     # Extract bounding box coordinates
#                     x1, y1, x2, y2 = map(int, box.xyxy[0])
#                     # Draw bounding box
#                     cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
#                     person_id = box.id[0] # Add a label with the person's ID
#                     cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)


#     # Capture the current timestamp for the frame processing
#     timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

#     # Initialize the session_id key in the `camera_data` dictionary if not already present
#     if session_id not in camera_data:
#             camera_data[session_id] = []

#     # Append the processed data to the session's list
#     camera_data[session_id].append((people_count, timestamp, session_id, hall_id))

#     print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")


if __name__ == '__main__':
    #camera_data = get_shared_camera_data()

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
    


