# # This file name is yolo_frame_processor, Formally asaaCopy.py >> you will be missed asaa :(
# import cv2
# import threading
# import datetime
# import time
# import mysql.connector
# from ultralytics import YOLO
# from shared_data import get_shared_camera_data



# # Initialize shared camera_data
# camera_data = get_shared_camera_data()

# # Define the model path
# # MODEL_PATH = '../../Yolo/yolo11s.pt'
# MODEL_PATH = '../../Yolo/thisIsTheBestWallah.pt'

# # Initialize the YOLO model
# model = YOLO(MODEL_PATH)

# # List to temporarily store people count data for each camera
# camera_data = {} #key: session_id, value: people_count, timestamp, hall_id

# # Database connection function
# def get_db_connection():
#     return mysql.connector.connect(
#         host="localhost",
#         user="root",
#         password="root",
#         database="raqeebdb"
#     )




# def save_to_database():
#     """
#     Periodically save data to MySQL every 5 minutes for each camera session.
#     """
#     while True:
#         time.sleep(6)  # Save every 6 seconds
#         db_connection = get_db_connection()
#         cursor = db_connection.cursor()
#         all_data_saved = True  # Flag to track if all insertions were successful

#         # Insert data for each camera session
#         for session, data in camera_data.items():
#             if data:  # Only proceed if there's data for this session
#                 data_to_save = [(entry[0], entry[1], entry[2]) for entry in data]
#                 print('from save to db:', data_to_save)
#                 query = "INSERT INTO peoplecount (Count, Time, SessionID) VALUES (%s, %s, %s)"
#                 try:
#                     cursor.executemany(query, data_to_save)
#                     # Only commit if insertion was successful
#                     db_connection.commit()
#                     print(f"Saved session {session} data to database.")
#                     camera_data[session].clear()  # Clear data for this session after saving
#                 except mysql.connector.Error as err:
#                     all_data_saved = False
#                     print(f"Error saving data for session {session}: {err}")
#                     db_connection.rollback()

#         # Print success message only if all insertions were successful
#         if all_data_saved:
#             print("All data successfully saved and cleared for all sessions.")
#         else:
#             print("Some data failed to save. Check error logs for details.")

#         cursor.close()
#         db_connection.close()


# # def save_to_database():
# #     """Periodically save data to MySQL every 5 minutes for each camera session."""
# #     while True:
# #         #time.sleep(600)  # Sleep for 10 minutes >> update to db every 10 min
# #         time.sleep(6)
# #         db_connection = get_db_connection()
# #         cursor = db_connection.cursor()
# #         all_data_saved = True  # Flag to track if all insertions are successful
# #         # Insert data for each camera session
# #         for session, data in camera_data.items():
# #             if data:  # Only proceed if there's data for this session
# #                 #print(session, data)
# #                 data_to_save = [(entry[0], entry[1], entry[2]) for entry in data]
# #                 #print('Count: ', camera_data[session][0], ' Time: ', camera_data[session][1],' SessionID: ', session)
# #                 #data_to_save = (camera_data[session][0], camera_data[session][1], session)
# #                 print('from save to db:', data_to_save)
# #                 query = "INSERT INTO peoplecount (Count, Time, SessionID) VALUES (%s, %s, %s)"
# #                 try:
# #                     cursor.executemany(query, data_to_save)
# #                     # Only commit if insertion was successful
# #                     db_connection.commit()
# #                     #print('WHY EMPTY ?? :(',camera_data)
# #                     camera_data[session].clear()  # Clear data for this session after saving
                    
# #                 except mysql.connector.Error as err:
# #                     all_data_saved = False
# #                     print(f"Error saving data for session {session}: {err}")
# #                     # rollback if there's an error (helps prevent partial updates)
# #                     db_connection.rollback()

# #         # Print success message only if all insertions were successful
# #         if all_data_saved:
# #             print("All data successfully saved and cleared for all sessions.")
# #         else:
# #             print("Some data failed to save. Check error logs for details.")

# #         db_connection.commit()
# #         cursor.close()
# #         db_connection.close()



# def process_frame(frame, session_id):
#     """
#     Process a frame to detect people, add bounding boxes, and log time, people count, and session ID.
#     """
#     results = model.track(frame, conf=0.45, persist=True, tracker="botsort.yaml")
#     people_count = 0

#     # Process detections to count people
#     for result in results:
#         for box in result.boxes:
#             if int(box.cls) == 1:  # Check if the detected class is 'person'
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

# def frame_handler(frame, session_id, hall_id):
#     """
#     Receives frames with a session ID from app.py, processes them, and stores people count with timestamp and session ID.
#     """
#     #print('Hello from frame_handler')
#     processed_data = process_frame(frame, session_id)
 
#     if session_id not in camera_data:
#             camera_data[session_id] = []  # Initialize if session_id is not yet in the dictionary
#             #key = session_id, value: people_count, timestamp

#     # Append the processed data to the session's list
#     camera_data[session_id].append((
#         processed_data["people_count"],
#         processed_data["timestamp"],
#         session_id,
#         hall_id
#         ))
#     #print('from frame_handler:',camera_data[session_id])
#     # return processed_data
#     print(f"DEBUG: Updated camera_data[{session_id}] = {camera_data[session_id]}")

# # Ensure manager and threads start only when executed as the main module
# if __name__ == "__main__":
#     db_thread = threading.Thread(target=save_to_database, daemon=True)
#     db_thread.start()

# #cv2.destroyAllWindows()
