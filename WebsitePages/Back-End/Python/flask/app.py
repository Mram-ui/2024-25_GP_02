#import datetime
from flask import Flask, render_template, Response, jsonify, send_from_directory, request
import mysql.connector
import cv2
import os
import time
from datetime import datetime
from datetime import timedelta
import threading


'''
This Dictionary is usefull when saving the model results 
by tracking all the 'Active' sessions for each hall.
Every 24hr a new session will be created for each hall.
'''
latest_session_id={} # key: hall_id, value: session_id

# Global dictionary to store camera status
camera_status = {}

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
            camera_status[camera['CameraName']] = "unknown"  # Initialize camera status


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
    return render_template('dashboard.html', cameras=detailed_camera_data, eventData=event_data, camera_status=camera_status, numOfHalls=numOfHalls)


# This function is called by video_feed 
def generate_frames(rtsp_link, CameraName):
    cap = cv2.VideoCapture(rtsp_link)

    # cap.setExceptionMode(True)
    # cap.set(cv.CAP_PROP_OPEN_TIMEOUT_MSEC, 1000)
    # cap.open("http://10.0.0.114")


    
    if not cap.isOpened():
        print(f"Failed to open RTSP link: {rtsp_link}")
        camera_status[CameraName] = "down"  # Mark camera status as 'failed'
        return

    camera_status[CameraName] = "up"  # Mark the camera as active
    
    while True:
        success, frame = cap.read()
        if not success:
            camera_status[CameraName] = "down"  # Mark the camera as disconnected if frame reading fails
            print('status down. Failed to fetch a frame')
            break


        # Stream the frame
        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()
        yield (b'--frame\r\n'
                b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

    cap.release()

def monitor_camera_connections():
    """Background thread to monitor the camera connections."""
    print('monitor_camera_connections Thread')
    while True:
        for CameraName, status in camera_status.items():
            if status == "down":
                print(f"Camera {CameraName} is down. Attempting reconnection...")
                # Optional: Implement reconnection logic here
        time.sleep(5)  # Check the connection status every 5 seconds



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
    return Response(generate_frames(rtsp_link, camera_info['CameraName']),
                    mimetype='multipart/x-mixed-replace; boundary=frame')


from datetime import datetime, timedelta
from flask import jsonify

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
        
        cursor.execute(query)
        data = cursor.fetchall()
        
        current_time = datetime.now()
        

        #This checks if the data is older than 10 seconds, if so it will be marked as 'No Recent Data'
        # for entry in data:
        #     if entry['Time']:
        #         latest_time = datetime.strptime(str(entry['Time']), "%Y-%m-%d %H:%M:%S")
        #         if (current_time - latest_time) > timedelta(seconds=10):
        #             entry['Count'] = 'No Recent Data'
        #         # else:
        #         #     # Update global dictionary with latest count
        #         #     bar_chart_data[entry['HallName']] = entry['Count']

        return jsonify(data)
    finally:
        cursor.close()
        connection.close()


@app.route('/get_all_thresholds', methods=['GET'])
def get_all_thresholds():
    """Fetch thresholds for all halls."""
    connection = get_db_connection()
    event_id = request.args.get('eventID')
    try:
        cursor = connection.cursor(dictionary=True)
        query = "SELECT HallID, HallThreshold AS Threshold FROM hall"
        cursor.execute(query)
        data = cursor.fetchall()  # Fetch all thresholds
        return jsonify(data), 200
    finally:
        cursor.close()
        connection.close()

@app.route('/camera_status', methods=['GET'])
def get_camera_status():
    """Endpoint to fetch the current status of all cameras."""
    return jsonify(camera_status)



#this function will send the hall names + current count
@app.route('/graph_data', methods=['GET'])
def graphs_data():
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)
    event_id = 101

    halls = retrieve_halls(event_id, cursor)

    # Initialize dictionaries to store the results
    bar_chart_data = {}
    brush_chart_data = {}
    hall_thresholds = {}
    pie_chart_data = {}
    line_chart_data = {}
    halls_pie_chart_data= {}
    try:
        for hall in halls:
            print(f"Processing hall: {hall['HallName']} (ID: {hall['HallID']})")

            # Query to get the latest people count for the session along with the hall name
            current_time = datetime.now()
            query = """
            SELECT count, Time
            FROM PeopleCount
            WHERE SessionID = (
                SELECT SessionID
                FROM monitoredsession
                WHERE hallID = %s
                ORDER BY SessionID DESC
                LIMIT 1
            )
            ORDER BY Time DESC
            LIMIT 1            
            """
            brush_chart_query = '''
            SELECT Time, count
            FROM PeopleCount
            WHERE SessionID IN (
                SELECT SessionID
                FROM monitoredsession
                WHERE hallID = %s
            )
            ORDER BY Time
            '''

            cursor.execute(query, (hall['HallID'],))
            bar_chart_result = cursor.fetchone()

            cursor.execute(brush_chart_query, (hall['HallID'],))
            brush_chart_result = cursor.fetchall()
            bar_chart_data[hall['HallName']] = bar_chart_result['count']
            # Store the threshold for the hall
            if hall['EventID'] == event_id:
                hall_thresholds[hall['HallName']] = hall['HallThreshold']


            # Format brush chart data: convert Time to milliseconds and pair with count
            if brush_chart_result:
                brush_chart_data[hall['HallName']] = [
                    [int(row['Time'].timestamp() * 1000), row['count']]  # Convert datetime to timestamp in milliseconds
                    for row in brush_chart_result
                ]
            else:
                brush_chart_data[hall['HallName']] = []

            # # If data was older than 10 seconds ago, it will be 0 in the bar chart
            # if bar_chart_result and 'Time' in bar_chart_result:
            #     latest_time = datetime.strptime(str(bar_chart_result['Time']), "%Y-%m-%d %H:%M:%S")
            #     if (current_time - latest_time) > timedelta(seconds=10):
            #         bar_chart_data[hall['HallName']] = 0
            #         brush_chart_data[hall['HallName']] = 0
            #     else:
            #         bar_chart_data[hall['HallName']] = bar_chart_result['count']
            # else:
            #     bar_chart_data[hall['HallName']] = 0



            halls_pie_chart = '''
                SELECT 
                    h.HallName,
                    ms.hallID, 
                    AVG(TIMESTAMPDIFF(SECOND, pt.EntranceTime, pt.ExitTime)) AS avg_duration_seconds
                FROM 
                    persontrack pt
                JOIN 
                    monitoredsession ms ON pt.SessionID = ms.SessionID
                JOIN
                    hall h ON ms.hallID = h.hallID
                GROUP BY 
                    ms.hallID, h.HallName;  
            '''

        cursor.execute(halls_pie_chart)

        # Fetch the results
        halls_pie_chart_results = cursor.fetchall()

        for row in halls_pie_chart_results:
            halls_pie_chart_data[row['HallName']] = row['avg_duration_seconds']
            print(f"Hall {row['HallName']}: Average Duration = {row['avg_duration_seconds']} seconds")
            


        # pie_chart_data['Main Hall'] = {'Female': 20, 'Male':10}
        # pie_chart_data['VIP'] = {'Female': 30, 'Male':10}

        # pie_chart_query= '''
        # SELECT Gender, COUNT(Gender) AS count
        # FROM PersonTrack
        # WHERE Gender IN ('male', 'female')
        # GROUP BY Gender;
        # '''

        # cursor.execute(pie_chart_query)
        # pie_chart_result = cursor.fetchall()

        # pie_chart_data = { "Female": 0, "Male": 0 }

        # for row in pie_chart_result:
        #     gender = row[0]
        #     count = row[1]
        #     if gender == "female":
        #         pie_chart_data["Female"] = count
        #     elif gender == "male":
        #         pie_chart_data["Male"] = count


        # Query to get gender counts per hall
        pie_chart_query = '''
        SELECT 
            h.HallName,
            pt.Gender,
            COUNT(pt.Gender) AS count
        FROM 
            PersonTrack pt
        JOIN 
            MonitoredSession ms ON pt.SessionID = ms.SessionID
        JOIN 
            Hall h ON ms.HallID = h.HallID
        WHERE 
            pt.Gender IN ('male', 'female')
        GROUP BY 
            h.HallName, pt.Gender;
        '''

        cursor.execute(pie_chart_query)
        pie_chart_result = cursor.fetchall()

        # Initialize pie_chart_data with hall names
        pie_chart_data = {}

        # Process the query results
        for row in pie_chart_result:
            try:
                hall_name = row['HallName']  # Access HallName from the dictionary
                gender = row['Gender']       # Access Gender from the dictionary
                count = row['count']         # Access count from the dictionary

                # Initialize the hall in pie_chart_data if it doesn't exist
                if hall_name not in pie_chart_data:
                    pie_chart_data[hall_name] = { "Female": 0, "Male": 0 }

                # Update the count for the corresponding gender
                if gender == "female":
                    pie_chart_data[hall_name]["Female"] = count
                elif gender == "male":
                    pie_chart_data[hall_name]["Male"] = count
            except Exception as e:
                print(f"Error processing row: {row}. Error: {e}")

        # ??? If no data is found, ensure pie_chart_data has at least one hall
        if not pie_chart_data:
            pie_chart_data = { "Main Hall": { "Female": 0, "Male": 0 } }

        print("Pie Chart Data:", pie_chart_data)

        

        line_chart_query = '''
        SELECT Time, SUM(count) as total_count
        FROM PeopleCount
        GROUP BY Time
        ORDER BY Time
        '''
        cursor.execute(line_chart_query)
        line_chart_result = cursor.fetchall()

        # Format the data for the line chart
        line_chart_data = [
            [int(row['Time'].timestamp() * 1000), row['total_count']]  # Convert datetime to milliseconds
            for row in line_chart_result
        ]

        # print("Line chart data:", line_chart_data)

        # print("Bar chart data:", bar_chart_data)
        # print("Brush chart data:", brush_chart_data)



    except Exception as e:
        print(f"An error occurred: {e}")
    finally:
        cursor.close()
        connection.close()
    # print('line_chart_data: ', line_chart_data)
    return jsonify({
        'hall_thresholds': hall_thresholds, # Include thresholds in the response
        'bar_chart_data': bar_chart_data,
        'brush_chart_data': brush_chart_data,
        'pie_chart_data': pie_chart_data,
        'line_chart_data': line_chart_data,
        'halls_pie_chart_data': halls_pie_chart_data,
    })


@app.route('/average_time_spent', methods=['GET'])
def average_time_spent():
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)

    try:
        # Query to calculate the average time spent in the whole event (in minutes)
        avg_time_spent_query = """
        SELECT AVG(total_time_spent) / 60 AS avg_time_spent_minutes
        FROM (
            SELECT ID, SUM(TIMESTAMPDIFF(SECOND, EntranceTime, ExitTime)) AS total_time_spent
            FROM persontrack
            WHERE ExitTime IS NOT NULL
            GROUP BY ID
        ) AS individual_times;
        """
        cursor.execute(avg_time_spent_query)
        avg_time_spent_result = cursor.fetchone()
        avg_time_spent_minutes = round(avg_time_spent_result['avg_time_spent_minutes'], 2) if avg_time_spent_result and 'avg_time_spent_minutes' in avg_time_spent_result else 0

    except Exception as e:
        print(f"An error occurred: {e}")
        avg_time_spent_minutes = 0
    finally:
        cursor.close()
        connection.close()

    return jsonify({
        'avg_time_spent_minutes': avg_time_spent_minutes,
    })


if __name__ == '__main__':
    #camera_data = get_shared_camera_data()
    # Start background thread to monitor camera connections
    threading.Thread(target=monitor_camera_connections, daemon=True).start()
    app.run(debug=True, port=5000)



# DO NOT DELETE IT -->
# This query retreives the total count of each hall, from the strat of the event
# It needs to be integrated with the tracker (for persistence in each frame for each person detected)
# query = f'''
# SELECT
#     h.HallID,
#     h.HallName,
#     COALESCE(SUM(pc.Count), 0) AS TotalVisitors
# FROM hall h
# LEFT JOIN monitoredsession ms ON h.HallID = ms.HallID
# LEFT JOIN peoplecount pc ON ms.SessionID = pc.SessionID
# WHERE h.HallID = "{hall['HallID']}"
# GROUP BY h.HallID, h.HallName
# '''
