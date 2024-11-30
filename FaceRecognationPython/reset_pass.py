from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import mysql.connector
import cv2
import numpy as np

app = Flask(__name__)

def connect_database():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="restoran"
    )

@app.route('/compare_faces', methods=['POST'])
def compare_faces():
    if 'uploaded_image' not in request.files or 'email' not in request.form:
        return jsonify({'error': 'Missing image or email'}), 400

    uploaded_image = request.files['uploaded_image']
    email = request.form['email']

    # Read uploaded image
    img = face_recognition.load_image_file(uploaded_image)
    uploaded_face_encoding = face_recognition.face_encodings(img)

    if not uploaded_face_encoding:
        return jsonify({'match': False, 'message': 'No face detected in uploaded image'})

    # Connect to database
    conn = connect_database()
    cursor = conn.cursor(dictionary=True)

    # Fetch stored face encoding for the email
    cursor.execute("SELECT id_pelanggan, image_face FROM pelanggan WHERE email = %s", (email,))
    result = cursor.fetchone()

    if not result or not result['image_face']:
        return jsonify({'match': False, 'message': 'No stored face for this email'})

    # Convert stored encoding back to numpy array
    stored_encoding = np.frombuffer(result['image_face'], dtype=np.float64)

    # Compare faces
    match = face_recognition.compare_faces([stored_encoding], uploaded_face_encoding[0])

    cursor.close()
    conn.close()

    return jsonify({
        'match': bool(match[0]),
        'message': 'Face comparison complete'
    })

if __name__ == '__main__':
    app.run(debug=True)