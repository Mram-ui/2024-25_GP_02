import cv2
import threading
import queue
from ultralytics import YOLO
import datetime
import numpy as np
import os
import pickle
from sklearn.metrics.pairwise import cosine_similarity
from deepface import DeepFace

MODEL_PATH = 'Yolo/yolo11s.pt'
VIDEO_SOURCES = [0]  # List of video sources
FEATURES_FILE = 'person_features.pkl'

# Initialize or load feature vectors and IDs
if os.path.exists(FEATURES_FILE):
    with open(FEATURES_FILE, 'rb') as f:
        person_features = pickle.load(f)
        next_person_id = max(person_features.keys(), default=0) + 1  # Set next_person_id to the next available ID
else:
    person_features = {}
    next_person_id = 1  # Starting ID if no features are loaded

# Shared frame queue
frame_queue = queue.Queue()
stop_thread = False

def extract_features(frame, bbox):
    x1, y1, x2, y2 = bbox
    person_crop = frame[y1:y2, x1:x2]
    try:
        embeddings = DeepFace.represent(person_crop, model_name='Facenet', enforce_detection=False)[0]['embedding']
        return np.array(embeddings)
    except Exception as e:
        print(f"Error extracting features: {e}")
        return None

def match_and_assign_id(features):
    global next_person_id  # Declare as global to access the global next_person_id
    if not person_features:
        person_id = next_person_id
        person_features[person_id] = features
        next_person_id += 1
        return person_id

    existing_features = np.array(list(person_features.values()))
    similarities = cosine_similarity([features], existing_features)[0]
    max_similarity = max(similarities)
    if max_similarity > 0.5:
        matched_id = list(person_features.keys())[np.argmax(similarities)]
        return matched_id

    person_id = next_person_id
    person_features[person_id] = features
    next_person_id += 1
    return person_id

def run_tracker_in_thread(model_path, video_source):
    global stop_thread
    model = YOLO(model_path)
    cap = cv2.VideoCapture(video_source)
    if not cap.isOpened():
        print(f"Error: Could not open video source {video_source}")
        stop_thread = True
        return

    while not stop_thread:
        ret, frame = cap.read()
        if not ret:
            break

        results = model(frame, agnostic_nms=True, conf=0.25)
        for result in results:
            for box in result.boxes:
                if int(box.cls) == 0:
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    features = extract_features(frame, (x1, y1, x2, y2))
                    if features is not None:
                        person_id = match_and_assign_id(features)
                        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                        cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

        frame_queue.put(frame)

    cap.release()

# Start the tracker thread
tracker_thread = threading.Thread(target=run_tracker_in_thread, args=(MODEL_PATH, VIDEO_SOURCES[0]), daemon=True)
tracker_thread.start()

while not stop_thread:
    if not frame_queue.empty():
        frame = frame_queue.get()
        cv2.imshow("YOLOv8 People Detection", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        stop_thread = True
        break

cv2.destroyAllWindows()
tracker_thread.join()

# Save features
with open(FEATURES_FILE, 'wb') as f:
    pickle.dump(person_features, f)
