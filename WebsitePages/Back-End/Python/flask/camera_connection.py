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

from event_processor import frame_handler

# Dictionary to track camera connection status of all the cameras of current event. on/off
camera_connection_status = {}

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

    def run(self):
        cap = cv2.VideoCapture(self.stream)
        if cap.isOpened():
            print(f"Camera {self.camera_id} is connected")
            while self.running:
                ret, frame = cap.read()
                cv2.imshow("Stream", frame)
                cv2.waitKey(1)  # Display the frame for a short time
                if ret:
                    if self.frames.full():
                        self.frames.get()  # drop the oldest frame
                    self.frames.put(frame)
                else:
                    time.sleep(0.1)  # minor wait to reduce tight looping
            cap.release()
        else:
            print(f"Camera {self.camera_id} failed to connect")

    def stop(self):
        self.running = False

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
        hall_id = camera['HallID']
        camera_id = camera['CameraID']
        event_id = camera['event_id']
        threading.Thread(target=read_frames, args=(camera['rtsp_link'], hall_id, camera_id, event_id), daemon=True).start()
        print(f"Camera thread for Hall Name: {camera['HallName']}, CameraID: {camera_id}, in HallID: {hall_id}")

def read_frames(rtsp_link, hall_id, camera_id, event_id):
    frames = Queue(maxsize=30)
    worker = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
    worker.start()

    while True:
        try:
            frame = frames.get(timeout=3)
            camera_connection_status[camera_id] = 'on'
            print(frame.shape)  # send to model or dashboard here
        except Empty:
            print(f"Queue empty for Camera {camera_id}")
            camera_connection_status[camera_id] = 'off'
            worker.stop()
            worker.join(timeout=2)
            if worker.is_alive():
                print(f"Warning: Camera {camera_id} thread did not shut down in time")
            worker = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
            worker.start()

def scheduler():
    sched = BackgroundScheduler(daemon=True)
    # You can add maintenance jobs here if needed
    sched.start()
    atexit.register(lambda: sched.shutdown())

if __name__ == '__main__':
    initiate_camera_connection()
    scheduler()

    try:
        print("Application is running. Press Ctrl+C to exit.")
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Application is shutting down.")



































































# from sched import scheduler
# import threading
# from threading import Thread
# from queue import Empty, Queue
# import time
# from datetime import datetime
# import datetime
# import time
# import cv2
# import mysql.connector
# from ultralytics import YOLO
# from apscheduler.schedulers.background import BackgroundScheduler
# import atexit
# import os

# from event_processor import frame_handler

# # Dictionary to track camera connection status of all the cameras of current event. on/off
# # camera_connection_status = {'camera_id': 'on/off'}
# camera_connection_status = {}


# class Worker (Thread):
#     def __init__(self, frames: Queue, stream, hall_id, camera_id, event_id):
#         super().__init__()
#         self.frames = frames #queue that stores captured frames
#         self.running = True # flag to stop the thread
#         self.stream = stream # rtsp link
#         self.hall_id = hall_id
#         self.camera_id = camera_id
#         self.event_id = event_id


#     # sending frames to model and dashboard should be handled here
#     # read_frames only handels camera connection issues
#     def run(self):
#         cap = cv2.VideoCapture(self.stream) 
#         if cap.isOpened():
#             print("Camera is connected")      
#             while self.running:
#                 ret, frame = cap.read()
#                 if ret:
#                     if self.frames.full():
#                         self.frames.get()
#                     self.frames.put(frame)
#             cap.release()  # Release the camera when stopping
#         else:
#             print("Camera is not connected")

#     def stop(self):
#         self.running = False                

# # Define database configuration
# db_config = {
#     'host': 'localhost',
#     'user': 'root',
#     'password': 'root',
#     'database': 'raqeebdb'
# }

# # Function to establish a database connection
# def get_db_connection():
#     connection = mysql.connector.connect(
#         host=db_config['host'],
#         user=db_config['user'],
#         password=db_config['password'],
#         database=db_config['database']
#     )
#     return connection

# # Retrieve camera details of current events
# def retrieve_camera_details():
#     connection = get_db_connection()

#     try:
#         cursor = connection.cursor(dictionary=True)
#         all_current_events = retrieve_current_events()
#         # List containing all data for each camera in an event
#         detailed_camera_data = []

#         for event in all_current_events:
#             # Retrieve camera information
#             halls_data = retrieve_halls(event['EventID'], cursor)

#             for hall in halls_data:
#                 camera_id = hall['CameraID']
#                 hall_id = hall['HallID'] # this is the key in latest_session_id, use it directly, it's already inside a loop
#                 numOfHalls = len(halls_data) # Count the number of halls
#                 # Retrieve all information for each CameraID
#                 cameras_data = retrieve_cameras(camera_id, cursor)
#                 # print("Latest Session ID Dictionary:", latest_session_id)

#                 # print('-------------------------------',latest_session_id[hall_id],'-----------------------------')
#                 for camera in cameras_data:
#                     rtsp_link = f"rtsp://{camera['CameraUsername']}:{camera['CameraPassword']}@{camera['CameraIPAddress']}:{camera['PortNo']}/{camera['StreamingChannel']}"
#                         # 'rtsp://cameraUserName:cameraPassword@CameraIPAddress:PortNo/StreamingChannel'
#                     detailed_camera_data.append({
#                         'HallID': hall_id,
#                         'HallName': hall['HallName'],
#                         'cameraName': camera['CameraName'],
#                         'CameraID': camera_id,
#                         'rtsp_link': rtsp_link,
#                         'event_id': event['EventID'],
#                     })

#         return detailed_camera_data

#     finally:
#         # Ensure resources are closed properly
#         cursor.close()
#         connection.close()

# # Retrieve all current events in the db
# def retrieve_current_events():

#     connection = get_db_connection()
#     try:
#         cursor = connection.cursor(dictionary=True)

#         # Define the current date and time
#         now = datetime.datetime.now()

#         # Query to fetch current events
#         query = '''
#             SELECT *
#             FROM events
#             WHERE
#                 (EventStartDate < %s OR (EventStartDate = %s AND EventStartTime <= %s))
#                 AND
#                 (EventEndDate > %s OR (EventEndDate = %s AND EventEndTime >= %s))
#         '''

#         # Execute the query with the current date and time
#         cursor.execute(query, (now.date(), now.date(), now.time(), now.date(), now.date(), now.time()))

#         # Fetch all current events
#         current_events = cursor.fetchall()

#         # Print and return the events
#         for event in current_events:
#             cursor.execute('SELECT HallID FROM hall WHERE EventID=%s', (event['EventID'],))
#             halls = cursor.fetchall()
#             event['Halls'] = halls  # Add halls as a new key to the event

#         # print('return current_events')
#         return current_events
#     finally:
#         # Ensure resources are closed properly
#         cursor.close()
#         connection.close()

# def retrieve_halls(event_id, cursor):
#     cursor.execute(f'SELECT * FROM hall WHERE EventID={event_id}')
#     halls_data = cursor.fetchall()

#     # returns list of halls
#     return halls_data

# def retrieve_cameras(camera_id, cursor):
#     cursor.execute(f'SELECT * FROM camera WHERE CameraID={camera_id}')
#     cameras_data = cursor.fetchall()

#     # returns list of cameras
#     return cameras_data


# #This function initiates reading for all active cameras
# #previously named start_frame_reading
# def initiate_camera_connection():
#         # Retrieve all cameras of current events
#         active_cameras = retrieve_camera_details()

#         for camera in active_cameras:
#             hall_id = camera['HallID']
#             camera_id = camera['CameraID']
#             event_id = camera['event_id']
#             threading.Thread(target=read_frames, args=(camera['rtsp_link'], hall_id, camera_id, event_id)).start()
#             print(f"Camera thread for Hall Name: {camera['HallName']}, CameraID: {camera_id}, in HallID: {hall_id}")


# # def scheduler():
# #     sched = BackgroundScheduler(daemon=True)
# #     sched.add_job(camera_connection_manager,'interval',second=10)  # initial run of scheduler is after 10 seconds
# #     sched.start()
# #     # print("Scheduler started. Latest Session ID updated:", latest_session_id)
# #     # Shut down the scheduler when exiting the app
# #     atexit.register(lambda: sched.shutdown())

# # # This function checks for disconnected cameras and restarts them
# # def camera_connection_manager():
# #     dwj

# def read_frames(rtsp_link, hall_id, camera_id, event_id):
#     '''
#     This function initiates the camera connection for a given camera.
#     It is called by the `initiate_camera_connection()` function using threading,
#     allowing simultaneous processing for multiple cameras.

#     1- connect to the camera using its RTSP link
#     2- ............ -send to app.py and e_p.py-
#     '''


#     frames = Queue(maxsize=30)
#     # def __init__(self, frames: Queue, stream, hall_id, camera_id, event_id):
#     th = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
#     th.start()

#     while True:
#         try:
#             frame = frames.get(timeout=3)
#             camera_connection_status[camera_id] = 'on'
#             print(frame.shape)
#         except Empty:# exception happen when frame is not captured within 3 seconds
#             print("queue empty")
#             camera_connection_status[camera_id] = 'off'
#             th.stop()  # Stop the old thread
#             th.join()  # Wait for the thread to finish
#             th = Worker(frames, rtsp_link, hall_id, camera_id, event_id)
#             th.start()  # Start a new one



# ##---------------------------------------------------------------------------------------------------------------------------------------------------------

#     # try:
#     #     camera_connection_status[camera_id] = 'off'
#     #     cap = cv2.VideoCapture(rtsp_link) # Connect to the camera

#     #     if cap.isOpened():
#     #         print("Camera is connected")
#     #         camera_connection_status[camera_id] = 'on'
#     #         fps = cap.get(cv2.CAP_PROP_FPS)  # Get frames per second of the video source


#     #         failed_frame_reading_counter = 0
#     #         while True:
#     #             success, frame = cap.read() # Read a frame from the video stream.
#     #             print('success', success)
#     #             if success:
#     #                 failed_frame_reading_counter = 0

#     #             if not success:
#     #                 failed_frame_reading_counter += 1
#     #                 print('failed_frame_reading_counter', failed_frame_reading_counter)
#     #                 print('camera_connection_status[camera_id] should be ONNNNNNN', camera_connection_status[camera_id])
#     #                 if failed_frame_reading_counter >= int(fps * 0.3):
#     #                     # If failed to read frames for fps * 0.1 consecutive times, report to camera_connection_manager
#     #                     camera_connection_status[camera_id] = 'off'
#     #                     print('camera_connection_status[camera_id] should be OFFFFFFFFFFF', camera_connection_status[camera_id])
#     #                     print(f"Failed to read (camera_id: {camera_id})")
#     #                     break
#     #     else: # cap.isOpened() == False
#     #         print("Camera is not connected")

# ##---------------------------------------------------------------------------------------------------------------------------------------------------------

#         # if cap is None or not cap.isOpened():
#         #     print(f"Failed to open RTSP link: {rtsp_link}")
#         #     return

#         # fps = cap.get(cv2.CAP_PROP_FPS)  # Get frames per second of the video source
#         # failed_frame_reading_counter = 0
#         # while True:
#         #     success, frame = cap.read() # Read a frame from the video stream.
#         #     if success:
#         #         failed_frame_reading_counter = 0
#         #     camera_connection_status[camera_id] = 'on'
#         #     if not success:
#         #         failed_frame_reading_counter += 1
#         #         if failed_frame_reading_counter >= fps * 0.1:
#         #             # If failed to read frames for 3 consecutive times, report to camera_connection_manager
#         #             camera_connection_status[camera_id] = 'off'
#         #             print(f"Failed to read (camera_id: {camera_id})")
#         #             break



#     except Exception as e:
#         print(f"Exception occurred in read_frames: {e}")



# if __name__ == '__main__':

#     initiate_camera_connection()
#     scheduler()

#     # Keep the main thread alive to allow the scheduler to run
#     try:
#         print("Application is running. Press Ctrl+C to exit.")
#         while True:
#             time.sleep(1)
#     except KeyboardInterrupt:
#         print("Application is shutting down.")
