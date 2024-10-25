import cv2
from ultralytics import YOLO

# Load the YOLOv8 model
model = YOLO('Yolo/bestWeights.pt')  

# Open the webcam
cap =cv2.VideoCapture(0)


if not cap.isOpened():
    print("Error: Could not open video stream from camera.")
    exit()

while True:
    # Capture frame-by-frame
    ret, frame = cap.read()
    if not ret:
        break

    # Detect people in the frame
    results = model(frame, agnostic_nms=True, conf=0.25 ) # Remove the double detection, adding threshod

    # Filter detections to only include people (class ID for person in COCO dataset is 0)
    people_count = 0
    for result in results:
        for box in result.boxes:
            if int(box.cls) == 1:  # Check if the detected class is person
                people_count += 1
                # Extract box coordinates
                x1, y1, x2, y2 = map(int, box.xyxy[0])
                # Draw bounding box arond the person
                cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

    # Display the number of people in the frame
    text = f"People count: {people_count}"
    cv2.putText(frame, text, (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 0, 0), 2)

    # Display the resulting frame with detections
    cv2.imshow('YOLOv8 People Detection', frame)

    # Break the loop if 'q' is pressed
    if cv2.waitKey(2) & 0xFF == ord('q'):
        break

# release the capture and close the window
cap.release()
cv2.destroyAllWindows()
