from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import mysql.connector
from mysql.connector import Error
import logging
import io

# Configure logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

app = Flask(__name__)

def connect_database():
    """Create database connection with error handling."""
    try:
        return mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="restoran"
        )
    except Error as e:
        logger.error(f"Database connection error: {e}")
        return None

def encode_face_to_vector(image):
    """Convert face image to 1x128 vector encoding and store as bytes."""
    face_encoding = face_recognition.face_encodings(image)
    if len(face_encoding) == 0:
        return None
    # Ensure proper serialization of the numpy array
    return face_encoding[0].astype(np.float64).tobytes()

@app.route('/register', methods=['POST'])
def register():
    """Register a new face with email."""
    try:
        if 'image' not in request.files or 'email' not in request.form:
            return jsonify({
                'status': 'error',
                'message': 'Image and email are required'
            }), 400

        image_file = request.files['image']
        email = request.form['email']

        # Read and process image
        image_stream = io.BytesIO(image_file.read())
        img = face_recognition.load_image_file(image_stream)
        
        # Get face encoding
        face_encoding = encode_face_to_vector(img)
        if face_encoding is None:
            return jsonify({
                'status': 'error',
                'message': 'No face detected in image'
            }), 400

        # Store in database
        conn = connect_database()
        if conn is None:
            return jsonify({
                'status': 'error',
                'message': 'Database connection failed'
            }), 500

        try:
            with conn.cursor() as cursor:
                # Check if email already exists
                cursor.execute("SELECT id_pelanggan FROM pelanggan WHERE email = %s", (email,))
                if cursor.fetchone():
                    return jsonify({
                        'status': 'error',
                        'message': 'Email already registered'
                    }), 400

                # Insert new record
                cursor.execute(
                    "INSERT INTO pelanggan (email, image_face) VALUES (%s, %s)",
                    (email, face_encoding)
                )
                conn.commit()

                return jsonify({
                    'status': 'success',
                    'message': 'Face registered successfully'
                })

        finally:
            conn.close()

    except Exception as e:
        logger.error(f"Registration error: {e}")
        return jsonify({
            'status': 'error',
            'message': f'Registration failed: {str(e)}'
        }), 500

@app.route('/compare_faces', methods=['POST'])
def compare_faces():
    """Enhanced face comparison endpoint with better error handling and logging."""
    logger.info("Received face comparison request")
    
    try:
        # Validate request
        if 'uploaded_image' not in request.files:
            return jsonify({
                'status': 'error',
                'message': 'No image file uploaded'
            }), 400
            
        if 'email' not in request.form:
            return jsonify({
                'status': 'error',
                'message': 'Email is required'
            }), 400

        # Read and process uploaded image
        image_stream = io.BytesIO(request.files['uploaded_image'].read())
        img = face_recognition.load_image_file(image_stream)
        
        # Get face encodings
        face_locations = face_recognition.face_locations(img)
        if not face_locations:
            return jsonify({
                'status': 'error',
                'message': 'No face detected in uploaded image'
            }), 400
            
        encodings = face_recognition.face_encodings(img, face_locations)
        if not encodings:
            return jsonify({
                'status': 'error',
                'message': 'Could not encode face from image'
            }), 400

        uploaded_face_encoding = encodings[0]
        email = request.form['email']

        # Get stored encoding from database
        conn = connect_database()
        if conn is None:
            return jsonify({
                'status': 'error',
                'message': 'Database connection failed'
            }), 500

        try:
            with conn.cursor(dictionary=True) as cursor:
                cursor.execute(
                    "SELECT image_face FROM pelanggan WHERE email = %s",
                    (email,)
                )
                result = cursor.fetchone()

                if not result or not result['image_face']:
                    return jsonify({
                        'status': 'error',
                        'message': 'No stored face found for this email'
                    }), 404

                # Convert stored encoding back to numpy array
                stored_encoding = np.frombuffer(result['image_face'], dtype=np.float64)
                
                # Ensure proper shape for comparison
                if len(stored_encoding) != 128:
                    return jsonify({
                        'status': 'error',
                        'message': 'Invalid stored face encoding'
                    }), 500

                # Reshape for face_recognition library
                stored_encoding = stored_encoding.reshape(1, -1)

                # Compare faces
                matches = face_recognition.compare_faces(
                    stored_encoding, 
                    uploaded_face_encoding,
                    tolerance=0.6
                )
                
                # Calculate confidence score
                face_distance = face_recognition.face_distance(
                    stored_encoding,
                    uploaded_face_encoding
                )[0]
                confidence = float(1 - face_distance)
                
                return jsonify({
                    'status': 'success',
                    'match': bool(matches[0]),
                    'confidence': confidence,
                    'message': 'Face comparison complete'
                })

        finally:
            conn.close()

    except Exception as e:
        logger.error(f"Face comparison error: {e}")
        return jsonify({
            'status': 'error',
            'message': f'Face comparison failed: {str(e)}'
        }), 500

if __name__ == '__main__':
    app.run(debug=True)