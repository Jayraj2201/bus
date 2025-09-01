from flask import Flask, send_from_directory, request, jsonify
import mysql.connector
from datetime import datetime
import os
import socket

app = Flask(__name__)

# ✅ Get absolute path to the "collage" folder dynamically
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
COLLAGE_DIR = os.path.join(BASE_DIR)  # Ensures Flask serves files from "collage/"

# ✅ Automatically detect local machine's IP
localhost_ip = socket.gethostbyname(socket.gethostname())  # ✅ More reliable method to get local IP

# ✅ MySQL Database Connection
db = mysql.connector.connect(
    host=os.getenv("DB_HOST", "localhost"),
    user=os.getenv("DB_USER", "root"),
    password=os.getenv("DB_PASS", ""),
    database=os.getenv("DB_NAME", "BusTracking"),
    port=int(os.getenv("DB_PORT", "3307"))
)
cursor = db.cursor()

# ✅ Create table if not exists
cursor.execute("""
    CREATE TABLE IF NOT EXISTS temporary_bus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        latitude FLOAT NOT NULL,
        longitude FLOAT NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )
""")
db.commit()

# ✅ Serve "index.html" correctly
@app.route('/')
def home():
    return send_from_directory(COLLAGE_DIR, "index.html")  # Loads index.html from collage/

# ✅ Serve all other static files dynamically
@app.route('/<path:filename>')
def serve_files(filename):
    file_path = os.path.join(COLLAGE_DIR, filename)
    
    if os.path.exists(file_path) and os.path.isfile(file_path):
        return send_from_directory(COLLAGE_DIR, filename)  # Correct folder
    else:
        return "Error 404: File Not Found", 404  # Clean error instead of crashing

# ✅ API Endpoint: Update Location
@app.route('/update_location', methods=['POST'])
def update_location():
    data = request.json
    if "lat" in data and "lng" in data:
        lat, lng = data["lat"], data["lng"]
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        cursor.execute("INSERT INTO temporary_bus (latitude, longitude, timestamp) VALUES (%s, %s, %s)", 
                       (lat, lng, timestamp))
        db.commit()

        return jsonify({"status": "success", "latitude": lat, "longitude": lng, "timestamp": timestamp})
    else:
        return jsonify({"status": "error", "message": "Invalid data"}), 400

# ✅ API Endpoint: Get Latest Location
@app.route('/get_location', methods=['GET'])
def get_location():
    cursor.execute("SELECT latitude, longitude, timestamp FROM temporary_bus ORDER BY id DESC LIMIT 1")
    latest = cursor.fetchone()

    if latest:
        return jsonify({
            "status": "success",
            "location": {
                "latitude": latest[0],
                "longitude": latest[1],
                "timestamp": latest[2].strftime("%Y-%m-%d %H:%M:%S")
            }
        })
    else:
        return jsonify({"status": "error", "message": "No location data available"}), 404

if __name__ == '__main__':
    print(f"Flask running on: http://{localhost_ip}:5000")  
    app.run(host=localhost_ip, port=5000, debug=True)  # ✅ Dynamically runs on local machine IP
