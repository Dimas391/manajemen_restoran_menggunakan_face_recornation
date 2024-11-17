from flask import Flask, request, jsonify
import cv2
import numpy as np
import mysql.connector
import face_recognition

app = Flask(__name__)

# Function to compare the uploaded face with faces in the database
def compare_faces(uploaded_image):
    # Encode the uploaded face
    uploaded_face_encoding = face_recognition.face_encodings(uploaded_image)
    
    # Return an error if no face is detected
    if len(uploaded_face_encoding) == 0:
        return {"status": "error", "message": "No face detected in uploaded image"}
    
    uploaded_face_encoding = uploaded_face_encoding[0]

    # Connect to the database
    conn = mysql.connector.connect(host="localhost", user="root", password="", database="restoran")
    cursor = conn.cursor()

    # Fetch faces from the database
    cursor.execute("SELECT id_pelanggan, nama_pelanggan, image_face FROM pelanggan")
    faces = cursor.fetchall()

    # Compare each database face with the uploaded face
    for face in faces:
        id_pelanggan, name, image_data = face
        known_face_image = np.frombuffer(image_data, np.uint8)
        known_face_image = cv2.imdecode(known_face_image, cv2.IMREAD_COLOR)
        
        # Encode the known face
        known_face_encoding = face_recognition.face_encodings(known_face_image)
        if len(known_face_encoding) > 0:
            known_face_encoding = known_face_encoding[0]
            match = face_recognition.compare_faces([known_face_encoding], uploaded_face_encoding)
            
            # Return success if a match is found
            if match[0]:
                return {"status": "success", "id_pelanggan": id_pelanggan, "name": name}
    
    return {"status": "error", "message": "No match found"}

# API endpoint for face recognition
@app.route('/recognize', methods=['POST'])
def recognize():
    try:
        # Get the image from the request
        file = request.files.get('image_face')
        if not file:
            return jsonify({"status": "error", "message": "No image provided"})

        # Convert the image for processing
        img = cv2.imdecode(np.frombuffer(file.read(), np.uint8), cv2.IMREAD_COLOR)

        # Detect faces in the image
        face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, 1.1, 4)

        if len(faces) == 0:
            return jsonify({"status": "error", "message": "No face detected"})

        # Perform face comparison
        result = compare_faces(img)

        # Return the comparison result
        return jsonify(result)

    except Exception as e:
        return jsonify({"status": "error", "message": f"Failed to process image: {str(e)}"})

if __name__ == '__main__':
    app.run(debug=True)
