import cv2
import threading
from ultralytics import YOLO
import datetime

# Define the model path
MODEL_PATH = 'Yolo/yolo11s.pt'
VIDEO_SOURCES = [0]  # List of video sources (0 for webcam, or file paths)

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

    # Get the FPS of the video source
    fps = cap.get(cv2.CAP_PROP_FPS)
    frames_to_skip = int(fps * 5)  # Calculate frames to skip for 5 seconds

    frame_counter = 0
    people_counts_with_time = []  # Array to store people count and time

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        # Increment the frame counter
        frame_counter += 1

        # Process only one frame every 5 seconds
        if frame_counter % frames_to_skip == 0:
            # Track people in the frame
            results = model.track(frame, conf=0.45, persist=True, tracker="botsort.yaml")

            # Filter detections to only include people (class ID for person in COCO dataset is 0)
            people_count = 0
            for result in results:
                for box in result.boxes:
                    if int(box.cls) == 0:  # Check if the detected class is 'person'
                        people_count += 1
                        if box.id is not None:
                            x1, y1, x2, y2 = map(int, box.xyxy[0])
                            # Draw bounding box
                            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

                            # Display the unique ID
                            person_id = box.id[0]
                            cv2.putText(frame, f"ID: {person_id}", (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)

            # Capture the current time
            current_time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            # Append the people count and time to the array
            people_counts_with_time.append({"time": current_time, "people_count": people_count})

            # Display the number of people in the frame
            text = f"People count: {people_count}"
            cv2.putText(frame, text, (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 0, 0), 2)

            # Show the frame
            cv2.imshow(f'YOLOv8 People Detection - Source {video_source}', frame)

        # Break if 'q' is pressed
        if cv2.waitKey(2) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

    # Print the stored people counts with times (for debugging or logging)
    for entry in people_counts_with_time:
        print(f"Time: {entry['time']}, People Count: {entry['people_count']}")

# Create and start threads for each video source
threads = []
for source in VIDEO_SOURCES:
    thread = threading.Thread(target=run_tracker_in_thread, args=(MODEL_PATH, source), daemon=True)
    threads.append(thread)
    thread.start()

# Wait for all threads to complete
for thread in threads:
    thread.join()

cv2.destroyAllWindows()
