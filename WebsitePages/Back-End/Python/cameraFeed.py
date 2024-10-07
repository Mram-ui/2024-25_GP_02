from flask import Flask, render_template, Response
import cv2

#Initializes a Flask application instance
app = Flask(__name__)

# Open the video streams (adjust the IP addresses and RTSP links as needed)
video1 = cv2.VideoCapture("rtsp://Raqeeb1:raqeebCCTV2025@192.168.8.46:554/stream1")
video2 = cv2.VideoCapture("rtsp://Raqeeb2:raqeebCCTV2025@192.168.8.45:554/stream1")


#continuously reads frames from the specified camera
def generate_frames(camera):
    while True:
        success, frame = camera.read()
        if not success:
            print("Failed to capture from camera")
            break
        else:
            # Add a print statement to check if frames are being captured
            print("Frame captured")
            frame = cv2.resize(frame, (640, 480))  # Resize the frame to a smaller size

            ret, buffer = cv2.imencode('.jpg', frame, [cv2.IMWRITE_JPEG_QUALITY, 50])  # Lower quality for smaller frames
            frame = buffer.tobytes()
            
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n\r\n')


@app.route('/')
def index():
    # Main page with video feeds
    return render_template('index.html')

@app.route('/video_feed1')
def video_feed1():
    # Route for the first video stream
    return Response(generate_frames(video1),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/video_feed2')
def video_feed2():
    # Route for the second video stream
    return Response(generate_frames(video2),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == "__main__":
    app.run(debug=True)
