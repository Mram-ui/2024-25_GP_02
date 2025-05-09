from sched import scheduler
import threading
from threading import Thread
from queue import Empty, Queue
import time
from datetime import datetime
import datetime
import time
import cv2
import mysql.connector
from ultralytics import YOLO
from apscheduler.schedulers.background import BackgroundScheduler
import atexit
import os

from camera_status import camera_connection_status, latest_frames, latest_session_id
from event_processor import frame_handler

# Dictionary to track camera connection status of all the cameras of current event. on/off
# camera_connection_status = {}
camera_threads = {}  # Key: camera_id, Value: Worker thread
camera_thread_locks = threading.Lock()  # To synchronize access



class Worker(Thread):
    def __init__(self, frames: Queue, stream, hall_id, camera_id, event_id):
        super().__init__()
        self.frames = frames
        self.running = True
        self.stream = stream
        self.hall_id = hall_id
        self.camera_id = camera_id
        self.event_id = event_id
        self.daemon = True  # Ensures thread will exit if main process exits
        self.cap = None  # Initialize here

    def run(self):
        self.cap = cv2.VideoCapture(self.stream)  # ✅ Assign to self.cap
        if self.cap.isOpened():
            camera_connection_status[self.camera_id] = 'on'
            print(f"Camera {self.camera_id} is connected")
            while self.running:
                ret, frame = self.cap.read()  # Use self.cap here too
                cv2.waitKey(1)
                if ret:
                    latest_frames[self.camera_id] = frame
                    if self.frames.full():
                        self.frames.get()
                    self.frames.put(frame)
                else:
                    camera_connection_status[self.camera_id] = 'off'
                    time.sleep(0.1)
            self.cap.release()
        else:
            print(f"Camera {self.camera_id} failed to connect")

    def stop(self):
        self.running = False

    def get_fps(self):
        if self.cap and self.cap.isOpened():
            fps = self.cap.get(cv2.CAP_PROP_FPS)
            return fps
        return None



def read_frames(rtsp_link, hall_id, camera_id, event_id):
    time.sleep(2)  # Allow time for camera to initialize
    frames = Queue(maxsize=30)
    worker = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
    print(f"######################### Starting camera thread for Hall {hall_id}, CameraID {camera_id}, EventID {event_id}, latest_session_id", latest_session_id[hall_id], "#########################")
    print("latest_session_id", latest_session_id)

    with camera_thread_locks:
        camera_threads[camera_id] = worker
    worker.start()
    frame_count = 0  # Counter for frames

    while True:
        try:
            frame = frames.get(timeout=15)
            camera_connection_status[camera_id] = 'on'
            latest_frames[camera_id] = frame  # Ensure this line exists

            fps = worker.get_fps()

            if fps is None or fps == 0:
                fps = 30  # default fallback FPS if unknown
            frames_to_skip = int(fps * 6)  # Calculate how many frames to skip for 5 seconds

            if frame_count % frames_to_skip == 0:
                # Process the frame here
                # print("@@@@@@@@@@@@@@@@I am frame_reader, passing the frame to frame_handler@@@@@@@@@@@@@@@@", fps)
                print("latest_session_id[hall_id]", latest_session_id[hall_id])

                # Call the frame handler function from event_processor.py
                # frame_handler(frame, latest_session_id[hall_id], hall_id, event_id)
                threading.Thread(target=frame_handler, args=(frame, latest_session_id[hall_id], hall_id, event_id)).start()

            frame_count += 1  # Increment the frame count

        except Empty:
            print(f"Queue empty for Camera {camera_id}")
            camera_connection_status[camera_id] = 'off'
            print(f'camera {camera_id} status {camera_connection_status[camera_id]}')

            # Stop old worker
            worker.stop()
            worker.join(timeout=2)
            if worker.is_alive():
                print(f"Warning: Camera {camera_id} thread did not shut down in time")
            # Start a new one
            worker = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
            with camera_thread_locks:
                camera_threads[camera_id] = worker
            worker.start()



# Define database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'root',
    'database': 'raqeebdb'
}

def get_db_connection():
    return mysql.connector.connect(**db_config)

def retrieve_camera_details():
    connection = get_db_connection()
    try:
        cursor = connection.cursor(dictionary=True)
        all_current_events = retrieve_current_events()
        detailed_camera_data = []

        for event in all_current_events:
            halls_data = retrieve_halls(event['EventID'], cursor)
            for hall in halls_data:
                camera_id = hall['CameraID']
                hall_id = hall['HallID']
                cameras_data = retrieve_cameras(camera_id, cursor)
                for camera in cameras_data:
                    rtsp_link = f"rtsp://{camera['CameraUsername']}:{camera['CameraPassword']}@{camera['CameraIPAddress']}:{camera['PortNo']}/{camera['StreamingChannel']}"
                    detailed_camera_data.append({
                        'HallID': hall_id,
                        'HallName': hall['HallName'],
                        'cameraName': camera['CameraName'],
                        'CameraID': camera_id,
                        'rtsp_link': rtsp_link,
                        'event_id': event['EventID'],
                    })
        return detailed_camera_data
    finally:
        cursor.close()
        connection.close()

def retrieve_current_events():
    connection = get_db_connection()
    try:
        cursor = connection.cursor(dictionary=True)
        now = datetime.datetime.now()
        query = '''
            SELECT * FROM events
            WHERE (EventStartDate < %s OR (EventStartDate = %s AND EventStartTime <= %s))
              AND (EventEndDate > %s OR (EventEndDate = %s AND EventEndTime >= %s))
        '''
        cursor.execute(query, (now.date(), now.date(), now.time(), now.date(), now.date(), now.time()))
        current_events = cursor.fetchall()
        for event in current_events:
            cursor.execute('SELECT HallID FROM hall WHERE EventID=%s', (event['EventID'],))
            event['Halls'] = cursor.fetchall()
        return current_events
    finally:
        cursor.close()
        connection.close()

def retrieve_halls(event_id, cursor):
    cursor.execute(f'SELECT * FROM hall WHERE EventID={event_id}')
    return cursor.fetchall()

def retrieve_cameras(camera_id, cursor):
    cursor.execute(f'SELECT * FROM camera WHERE CameraID={camera_id}')
    return cursor.fetchall()




def initiate_camera_connection():
    active_cameras = retrieve_camera_details()
    for camera in active_cameras:
        if camera['CameraID'] not in camera_threads or not camera_threads[camera['CameraID']].is_alive():
            threading.Thread(
                target=read_frames,
                args=(camera['rtsp_link'], camera['HallID'], camera['CameraID'], camera['event_id']),
                daemon=True
            ).start()



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
                    print(f"Failed to insert HallID {hall['HallID']}")
        
        # Update latest_session_id_list 
        latest_session_id.update(temp_session_id)
        # Print the updated latest_session_id
        print("Updated latest_session_id:", latest_session_id)
        
    finally:
        # Ensure resources are properly closed
        cursor.close()
        connection.close()


# def scheduler():
#     sched = BackgroundScheduler(daemon=True)
#     # You can add maintenance jobs here if needed
#     sched.start()
#     atexit.register(lambda: sched.shutdown())

# if __name__ == '__main__':
#     initiate_camera_connection()
#     scheduler()

#     try:
#         print("Application is running. Press Ctrl+C to exit.")
#         while True:
#             time.sleep(1)
#     except KeyboardInterrupt:
#         print("Application is shutting down.")
