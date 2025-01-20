import cv2
import threading
from ultralytics import YOLO
import datetime
import numpy as np
import os
import pickle
from sklearn.metrics.pairwise import cosine_similarity
from deepface import DeepFace  # Import a real re-ID model for feature extraction

# Load the YOLOv8 model
MODEL_PATH = 'Yolo/yolo11s.pt'
VIDEO_SOURCES = [0]  # List of video sources (0 for webcam, or file paths)

# Path to store feature vectors and IDs
FEATURES_FILE = 'person_features.pkl'

# Initialize or load feature vectors and IDs
if os.path.exists(FEATURES_FILE):
    with open(FEATURES_FILE, 'rb') as f:
        person_features = pickle.load(f)
else:
    person_features = {}
    next_person_id = 1

# Function to extract features using DeepFace
def extract_features(frame, bbox):
    """
    Extract features from the bounding box of a person in the frame using DeepFace.

    Args:
        frame (np.ndarray): The current video frame.
        bbox (tuple): Bounding box coordinates (x1, y1, x2, y2).

    Returns:
        np.ndarray: Feature vector for the person.
    """
    x1, y1, x2, y2 = bbox
    person_crop = frame[y1:y2, x1:x2]
    try:
        # Use DeepFace to extract features
        embeddings = DeepFace.represent(person_crop, model_name='Facenet')[0]['embedding']
        return np.array(embeddings)
    except Exception as e:
        print(f"Error extracting features: {e}")
        return None

# Function to match features and assign IDs
def match_and_assign_id(features):
    """
    Match the extracted features to existing ones and assign a consistent ID.

    Args:
        features (np.ndarray): Extracted feature vector for the person.

    Returns:
        int: Assigned person ID.
    """
    global next_person_id
    if not person_features:
        # No existing features; assign a new ID
        person_id = next_person_id
        person_features[person_id] = features
        next_person_id += 1
        return person_id

    # Compute similarity with existing features
    existing_features = np.array(list(person_features.values()))
    similarities = cosine_similarity([features], existing_features)[0]

    # Check if any similarity exceeds a threshold
    max_similarity = max(similarities)
    if max_similarity > 0.5:
        matched_id = list(person_features.keys())[np.argmax(similarities)]
        return matched_id

    # No match found; assign a new ID
    person_id = next_person_id
    person_features[person_id] = features
    next_person_id += 1
    return person_id

# Function to run YOLO tracker in a thread
def run_tracker_in_thread(model_path, video_source):
    """
    Run YOLO tracker in a thread for concurrent processing.

    Args:
        model_path (str): Path to the YOLO model.
        video_source: Identifier for the webcam or path to the video file.
    """
    model = YOLO(model_path)
    cap = cv2.VideoCapture(video_source)

    if not cap.isOpened():
        print(f"Error: Could not open video source {video_source}")
        return

    frame_counter = 0
    people_counts_with_time = []  # Array to store people count and time

    # Get the FPS of the video source
    fps = cap.get(cv2.CAP_PROP_FPS)
    frames_to_skip = int(fps * 5)  # Calculate frames to skip for 5 seconds

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        # Detect people in the frame
        results = model(frame, agnostic_nms=True, conf=0.25)
        people_count = 0

        # Filter detections to include only people (class ID for person in COCO dataset is 0)
        for result in results:
            for box in result.boxes:
                if int(box.cls) == 0:  # Check if the detected class is 'person'
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    features = extract_features(frame, (x1, y1, x2, y2))
                    if features is not None:
                        person_id = match_and_assign_id(features)

                        # Draw bounding box
                        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

                        # Display the unique ID
                        cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

                        people_count += 1

        # Capture the current time
        current_time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        # Append the people count and time to the array
        people_counts_with_time.append({"time": current_time, "people_count": people_count})

        # Display the number of people in the frame
        text = f"People count: {people_count}"
        cv2.putText(frame, text, (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 0, 0), 2)

        # Show the frame
        cv2.imshow(f'YOLOv8 People Detection - Source {video_source}', frame)

        # Process only one frame every 5 seconds
        if frame_counter % frames_to_skip == 0:
            results = model.track(frame, conf=0.45, persist=True, tracker="botsort.yaml")

        frame_counter += 1

        # Break if 'q' is pressed
        if cv2.waitKey(2) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

    # Print the stored people counts with times (for debugging or logging)
    for entry in people_counts_with_time:
        print(f"Time: {entry['time']}, People Count: {entry['people_count']}")

    # Save updated features to file
    with open(FEATURES_FILE, 'wb') as f:
        pickle.dump(person_features, f)

# Create and start threads for each video source
threads = []
for source in VIDEO_SOURCES:
    thread = threading.Thread(target=run_tracker_in_thread, args=(MODEL_PATH, source), daemon=True)
    threads.append(thread)
    thread.start()

# Wait for all threads to complete
for thread in threads:
    thread.join()