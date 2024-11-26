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
# YOLO model path
MODEL_PATH = '../../Yolo/thisIsTheBestWallah.pt'
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
                    detailed_camera_data.append({
                        'HallID': hall_id,
                        'HallName': hall['HallName'],
                        'cameraName': camera['CameraName'],
                        'CameraID': camera_id,
                        'rtsp_link': rtsp_link,
                        #  'SessionID': session_id, from gList>>>> latest_session_id[hall_id]
                        'SessionID': latest_session_id[hall_id],
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


# This function is called by 2 function: initialize_camera_threads and video_feed 
def generate_frames(rtsp_link, session_id, hall_id):
    try:
        #print('hi from generate_frames')
        cap = cv2.VideoCapture(rtsp_link)
        print(f"Starting frame generation for SessionID: {session_id}")
        if not cap.isOpened():
            print(f"Failed to open RTSP link: {rtsp_link}")
            return
        fps = cap.get(cv2.CAP_PROP_FPS)  # Get frames per second of the video source
        frames_to_skip = int(fps * 3)  # Calculate how many frames to skip for 5 seconds
        frame_count = 0  # Counter for frames
        while True:
            success, frame = cap.read()
            if not success:
                break

            # Only process every 5 seconds based on frames_to_skip
            if frame_count % frames_to_skip == 0:
                #print('frame sent to frame_handler')
                threading.Thread(target=frame_handler, args=(frame.copy(), latest_session_id[hall_id], hall_id)).start()

            frame_count += 1  # Increment the frame count

    except Exception as e:
        print(f"Exception occurred in generate_frames: {e}")



def start_frame_reading():
    #connection = get_db_connection()
    #cursor = connection.cursor(dictionary=True)
    all_cameras = retrieve_camera_details()  # Retrieve all cameras
    
    for camera in all_cameras:
        #rtsp_link = f"rtsp://{camera['CameraUsername']}:{camera['CameraPassword']}@{camera['CameraIPAddress']}:{camera['PortNo']}/{camera['StreamingChannel']}"
        hall_id = camera['HallID']
        # session_id = latest_session_id.get(hall_id, None)
        session_id = camera['SessionID']

        if session_id:
            #print(session_id)
            print(camera['rtsp_link'])
            threading.Thread(target=generate_frames, args=(camera['rtsp_link'], session_id, hall_id)).start()
            print(f"Started reading frames for CameraID: {camera['CameraID']}, SessionID: {session_id}, in HallID: {hall_id}")



    
def scheduler():
    sched = BackgroundScheduler(daemon=True)
    sched.add_job(session_scheduler,'interval',seconds=60, next_run_time=datetime.datetime.now())
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
    Periodically save data to MySQL every 5 minutes for each camera session.
    """
    while True:
        time.sleep(6)  # Save every 6 seconds
        db_connection = get_db_connection()
        cursor = db_connection.cursor()
        all_data_saved = True  # Flag to track if all insertions were successful

        # Insert data for each camera session
        for session, data in camera_data.items():
            if data:  # Only proceed if there's data for this session
                data_to_save = [(entry[0], entry[1], entry[2]) for entry in data]
                print('from save to db:', data_to_save)
                query = "INSERT INTO peoplecount (Count, Time, SessionID) VALUES (%s, %s, %s)"
                try:
                    cursor.executemany(query, data_to_save)
                    # Only commit if insertion was successful
                    db_connection.commit()
                    print(f"Saved session {session} data to database.")
                    camera_data[session].clear()  # Clear data for this session after saving
                except mysql.connector.Error as err:
                    all_data_saved = False
                    print(f"Error saving data for session {session}: {err}")
                    db_connection.rollback()

        # Print success message only if all insertions were successful
        if all_data_saved:
            print("All data successfully saved and cleared for all sessions.")
        else:
            print("Some data failed to save. Check error logs for details.")

        cursor.close()
        db_connection.close()


# def process_frame(frame, session_id):
#     """
#     Process a frame to detect people, add bounding boxes, and log time, people count, and session ID.
#     """
#     results = model.track(frame, conf=0.45, persist=True, tracker="botsort.yaml")
#     people_count = 1

#     # Process detections to count people
#     for result in results:
#         for box in result.boxes:
#             if int(box.cls) == 0:  # Check if the detected class is 'person'
#                 people_count += 1
#                 if box.id is not None:
#                     x1, y1, x2, y2 = map(int, box.xyxy[0])
#                     # Draw bounding box
#                     cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
#                     person_id = box.id[0]
#                     cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)


#     # Get the current time
#     current_time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

#     # Return data as a dictionary
#     return {"people_count": people_count, "timestamp": current_time, "session_id": session_id}

def frame_handler(frame, session_id, hall_id):
    """
    Receives frames with a session ID from app.py, processes them, and stores people count with timestamp and session ID.
    """
    # #print('Hello from frame_handler')
    # processed_data = process_frame(frame, session_id)
    """
    Process a frame to detect people, add bounding boxes, and log time, people count, and session ID.
    """
    results = model.track(frame, conf=0.45, persist=True, tracker="botsort.yaml")
    people_count = 0

    # Process detections to count people
    for result in results:
        for box in result.boxes:
            if int(box.cls) == 1:  # Check if the detected class is 'person'
                people_count += 1
                if box.id is not None:
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    # Draw bounding box
                    cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                    person_id = box.id[0]
                    cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)


    # Get the current time
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    if session_id not in camera_data:
            camera_data[session_id] = []  # Initialize if session_id is not yet in the dictionary
            #key = session_id, value: people_count, timestamp

    # Append the processed data to the session's list
    camera_data[session_id].append((
        people_count,
        timestamp,
        session_id,
        hall_id
        ))
    #print('from frame_handler:',camera_data[session_id])
    # return processed_data
    print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")


if __name__ == '__main__':
    #camera_data = get_shared_camera_data()

    scheduler()
    print('before sleep')
    time.sleep(0.5)
    print('after sleep')
    start_frame_reading()
    db_thread = threading.Thread(target=save_to_database, daemon=True).start()

    # Keep the main thread alive to allow the scheduler to run
    try:
        print("Application is running. Press Ctrl+C to exit.")
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Application is shutting down.")
    


# DELETE FROM `peoplecount`;
# ALTER TABLE peoplecount AUTO_INCREMENT = 0 