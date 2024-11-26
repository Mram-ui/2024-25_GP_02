#import datetime
from flask import Flask, render_template, Response, jsonify, send_from_directory, request
import mysql.connector
import cv2
import os
import time
from datetime import datetime
from datetime import timedelta

'''
This Dictionary is usefull when saving the model results 
by tracking all the 'Active' sessions for each hall.
Every 24hr a new session will be created for each hall.
'''
latest_session_id={} # key: hall_id, value: session_id

data={}

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
        now = datetime.now()

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
                    })

        return detailed_camera_data

    finally:
        # Ensure resources are closed properly
        cursor.close()
        connection.close()
    






app = Flask(__name__)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
IMAGES_DIR = os.path.join(BASE_DIR, '../images')
FRONTEND_DIR = os.path.join(BASE_DIR, '../Front-End')
BACKEND_DIR = os.path.join(BASE_DIR, '../Back-End')

# Route to serve images from the external directory
@app.route('/images/<path:filename>')
def serve_images(filename):
    return send_from_directory(IMAGES_DIR, filename)

# Route to serve frontend files
@app.route('/Front-End/<path:filename>')
def serve_frontend(filename):
    return send_from_directory(FRONTEND_DIR, filename)

# Route to serve backend files
@app.route('/Back-End/<path:filename>')
def serve_backend(filename):
    return send_from_directory(BACKEND_DIR, filename)





# This function starts when dashboard.html load
@app.route('/')
def home():
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)
    # Get the eventId parameter from the URL
    event_id = request.args.get('eventID')
    
    #detailed_camera_data = retrieve_camera_details()
    # Retrieve camera information
    halls_data = retrieve_halls(event_id, cursor)


    # List containing all data for each camera in an event
    detailed_camera_data = []

    for hall in halls_data:
        camera_id = hall['CameraID']
        hall_id = hall['HallID'] # this is the key in latest_session_id, use it directly, it's already inside a loop
        numOfHalls = len(halls_data) # Count the number of halls
        # Retrieve all information for each CameraID
        cameras_data = retrieve_cameras(camera_id, cursor)

        for camera in cameras_data:
            rtsp_link = f"rtsp://{camera['CameraUsername']}:{camera['CameraPassword']}@{camera['CameraIPAddress']}:{camera['PortNo']}/{camera['StreamingChannel']}"
            detailed_camera_data.append({
                'hall_id': hall_id,
                'HallName': hall['HallName'],
                'cameraName': camera['CameraName'],
                'CameraID': camera_id,
                'rtsp_link': rtsp_link,
                'eventID' : event_id,
            })


    # Retrieve event information
    event_data = retrieve_events(event_id, cursor)

    # Convert datetime and timedelta to string for JSON serialization
    for row in event_data:
        for key, value in row.items():
            if isinstance(value, (datetime, timedelta)):
                row[key] = str(value)  # Convert to string

    cursor.close()
    connection.close()

    # Pass the camera data to the template
    return render_template('dashboard.html', cameras=detailed_camera_data, eventData=event_data, numOfHalls=numOfHalls)



# This function is called by 2 function: initialize_camera_threads and video_feed 
def generate_frames(rtsp_link):
    cap = cv2.VideoCapture(rtsp_link)
    
    while True:
        success, frame = cap.read()
        if not success:
            break


        # Stream the frame
        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()
        yield (b'--frame\r\n'
                b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')


# This function is accessed from dashboard.html to display cameras feeds
@app.route('/video_feed/<camera_id>')
def video_feed(camera_id):
    try:
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        
        # Retrieve RTSP link for the given camera_id
        cursor.execute(f'SELECT * FROM camera WHERE CameraID={camera_id}')
        camera_info = cursor.fetchone()
        rtsp_link = f"rtsp://{camera_info['CameraUsername']}:{camera_info['CameraPassword']}@{camera_info['CameraIPAddress']}:{camera_info['PortNo']}/{camera_info['StreamingChannel']}"

    finally:
        cursor.close()
        connection.close()

    # Use the RTSP link to stream the video
    return Response(generate_frames(rtsp_link),
                    mimetype='multipart/x-mixed-replace; boundary=frame')


# @app.route('/live_count', methods=['GET'])
# def live_count():
#     """
#     API endpoint to fetch live people count data for each hall using HallID.
#     """
#     global camera_data
#     live_counts = []
#     print("DEBUG: live_count API called")
#     print(f"DEBUG: latest_session_id = {latest_session_id}")
#     print(f"DEBUG: camera_data keys = {list(camera_data.keys())}")

#     # Ensure camera_data and latest_session_id are aligned
#     for session_id, data in camera_data.items():
#         if data:  # Ensure there's data for the session
#             last_entry = data[-1]  # Get the latest count
#             # Find the HallID for this SessionID
#             hall_id = None
#             for h_id, s_id in latest_session_id.items():
#                 if s_id == session_id:
#                     hall_id = h_id
#                     break
            
#             if hall_id is not None:
#                 live_counts.append({
#                     "hall_id": hall_id,                  # Use HallID
#                     "people_count": last_entry[0],       # Latest people count
#                     "timestamp": last_entry[1],          # Timestamp of the count
#                 })
#                 print(f"DEBUG: HallID {hall_id} -> People Count {last_entry[0]}")
#             else:
#                 print(f"DEBUG: No HallID found for SessionID {session_id}")

#     print(f"DEBUG: live_counts = {live_counts}")
#     return jsonify(live_counts)

@app.route('/latest_people_count', methods=['GET'])
def latest_people_count():
    """Fetch the latest count of people in each hall."""
    connection = get_db_connection()
    try:
        cursor = connection.cursor(dictionary=True)
        query = """
            SELECT 
                hall.HallID,
                hall.HallName,
                pc.Count,
                pc.Time
            FROM hall
            LEFT JOIN MonitoredSession ms ON hall.HallID = ms.HallID
            LEFT JOIN (
                SELECT 
                    SessionID, 
                    MAX(Time) AS LatestTime
                FROM peoplecount
                GROUP BY SessionID
            ) latest ON ms.SessionID = latest.SessionID
            LEFT JOIN peoplecount pc ON latest.SessionID = pc.SessionID AND latest.LatestTime = pc.Time
        """
        query = """
            SELECT 
                hall.HallID,
                hall.HallName,
                pc.Count,
                pc.Time
            FROM hall
            LEFT JOIN (
                SELECT 
                    HallID, 
                    MAX(SessionID) AS LatestSessionID
                FROM MonitoredSession
                GROUP BY HallID
            ) latest_session ON hall.HallID = latest_session.HallID

            LEFT JOIN (
                SELECT 
                    SessionID, 
                    MAX(Time) AS LatestTime
                FROM peoplecount
                GROUP BY SessionID
            ) latest_count ON latest_session.LatestSessionID = latest_count.SessionID

            LEFT JOIN peoplecount pc 
                ON latest_count.SessionID = pc.SessionID 
                AND latest_count.LatestTime = pc.Time;
        """

        # 1- identifies the latest timestamp (LatestTime) for each SessionID
        # 2- associate each hall with its corresponding SessionID and the latest timestamp (LatestTime)
        # 3- Join the results

        cursor.execute(query)
        data = cursor.fetchall()
        return jsonify(data)
    finally:
        cursor.close()
        connection.close()

if __name__ == '__main__':
    #camera_data = get_shared_camera_data()
    app.run(debug=True, port=5000)
