# In camera_status.py, add:
from queue import Queue

latest_session_id={} # key: hall_id, value: session_id
camera_connection_status = {}     # camera_id: "on"/"off"
latest_frames = {}                # camera_id: latest_frame (as a NumPy array)