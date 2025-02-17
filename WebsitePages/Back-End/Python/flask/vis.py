from flask import Flask, render_template, jsonify
import random
import datetime
import time
import threading

app = Flask(__name__)

# Data storage for each hall
data = {"Hall A": [], "Hall B": []}

# Function to generate random data
def generate_data():
    while True:
        now = datetime.datetime.now()
        time_ms = int(now.timestamp() * 1000)  # Convert to milliseconds

        # Generate random temperature data
        temp_A = random.uniform(20, 45)
        temp_B = random.uniform(25, 40)

        # Append new data
        data["Hall A"].append({"time": time_ms, "count": temp_A})
        data["Hall B"].append({"time": time_ms, "count": temp_B})

        # Keep only the last 50 entries
        if len(data["Hall A"]) > 500:
            data["Hall A"].pop(0)
        if len(data["Hall B"]) > 500:
            data["Hall B"].pop(0)

        time.sleep(10)  # Sleep for 10 seconds

# Route to serve the main page
@app.route('/')
def index():
    return render_template('vis.html')

# Route to fetch data for a specific city
@app.route('/data/<city>')
def get_data(city):
    return jsonify(data.get(city, []))

# Run the Flask app
if __name__ == '__main__':
    # Start the data generation thread
    threading.Thread(target=generate_data, daemon=True).start()
    app.run(debug=True)