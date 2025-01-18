import cv2
import os
from datetime import datetime
from ultralytics import YOLO
import time

# Model path and initialization
MODEL_PATH = '../../../../Yolo/best.pt'
model = YOLO(MODEL_PATH)

# RTSP link for static camera
RTSP_LINK = "rtsp://Raqeeb1:raqeebCCTV2025@192.168.8.75:554/stream1"

# Static event_id and session_id for testing
EVENT_ID = 'event'
SESSION_ID = 'session'

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

def process_frame(frame, event_id, session_id):
    """
    Process a single frame to detect persons and save cropped images.

    Parameters:
    - frame: The input video frame.
    - event_id: The event ID.
    - session_id: The session ID.
    """
    # Run YOLO detection
    results = model.track(frame, conf=0.3, classes=0, persist=True, tracker="botsort.yaml")  # Only detect the 'person' class (class 0)
    
    for box in results[0].boxes:
        if int(box.cls) == 0:  # If the detected class is 'person'
            # Extract bounding box coordinates
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            person_id = box.id[0] if box.id is not None else "unknown"
            
            # Crop the person from the frame
            crop = frame[y1:y2, x1:x2]
            
            # Generate a timestamp for the filename
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S_%f")
            
            # Save the cropped image
            save_cropped_image(crop, event_id, session_id, person_id, timestamp)

            # Optionally, draw the bounding box and label on the frame (for visualization)
            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
            cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

    return frame

def main():
    # Open the RTSP video stream
    video = cv2.VideoCapture(RTSP_LINK)

    if not video.isOpened():
        print("Failed to open RTSP video stream")
        return

    while True:
        ret, frame = video.read()
        
        if not ret:
            print("Failed to capture video stream")
            break

        # Process the frame and detect persons
        processed_frame = process_frame(frame, EVENT_ID, SESSION_ID)

        # Display the processed frame
        cv2.imshow("RTSP Video", processed_frame)

        time.sleep(2)

        # Exit the loop if 'q' is pressed
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    # Release the video stream and close windows
    video.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    main()
