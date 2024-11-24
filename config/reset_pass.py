from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import mysql.connector
from mysql.connector import Error
import logging
import io
from PIL import Image

# Configure logging
logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
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

def preprocess_image(image_data):
    """
    Preprocess image to standardize size and quality for face detection.
    """
    try:
        img = Image.open(io.BytesIO(image_data.read()))
        if img.mode != 'RGB':
            img = img.convert('RGB')
        max_size = 1024
        img.thumbnail((max_size, max_size), Image.LANCZOS)
        return img
    except Exception as e:
        logger.error(f"Image preprocessing error: {e}")
        return None

def get_face_encoding(image):
    """
    Get face encoding from the image.
    """
    try:
        image_np = np.array(image)
        face_locations = face_recognition.face_locations(image_np)
        if not face_locations:
            logger.warning("No face detected in the image.")
            return None
        encodings = face_recognition.face_encodings(image_np, face_locations)
        return encodings[0] if encodings else None
    except Exception as e:
        logger.error(f"Face encoding error: {e}")
        return None

def normalize_encoding(encoding):
    """
    Normalize face encoding to standard format.
    """
    try:
        encoding = np.array(encoding, dtype=np.float64)
        encoding = encoding.flatten()
        if len(encoding) > 128:
            encoding = encoding[:128]
        elif len(encoding) < 128:
            encoding = np.pad(encoding, (0, 128 - len(encoding)), mode='constant')
        norm = np.linalg.norm(encoding)
        return encoding / norm if norm > 0 else encoding
    except Exception as e:
        logger.error(f"Encoding normalization error: {e}")
        return None

def compare_encodings(encoding1, encoding2):
    """
    Compare face encodings with multiple similarity metrics.
    """
    try:
        encoding1 = normalize_encoding(encoding1)
        encoding2 = normalize_encoding(encoding2)
        if encoding1 is None or encoding2 is None:
            return False, 0.0
        euclidean_distance = np.linalg.norm(encoding1 - encoding2)
        similarity_score = 1 / (1 + euclidean_distance)
        return similarity_score > 0.5, float(similarity_score)  # Adjust threshold as needed
    except Exception as e:
        logger.error(f"Encoding comparison error: {e}")
        return False, 0.0

@app.route('/register', methods=['POST'])
def register():
    """Register a new face with email."""
    if 'image' not in request.files or 'email' not in request.form:
        return jsonify({'status': 'error', 'message': 'Image and email are required'}), 400

    try:
        image_file = request.files['image']
        email = request.form['email']
        image = preprocess_image(image_file)
        if image is None:
            return jsonify({'status': 'error', 'message': 'Invalid image format'}), 400

        face_encoding = get_face_encoding(image)
        if face_encoding is None:
            return jsonify({'status': 'error', 'message': 'No face detected in image'}), 400

        face_encoding = normalize_encoding(face_encoding)
        if face_encoding is None:
            return jsonify({'status': 'error', 'message': 'Error processing face encoding'}), 400

        conn = connect_database()
        if conn is None:
                      return jsonify({'status': 'error', 'message': 'Database connection failed'}), 500

        try:
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO pelanggan (email, image_face) VALUES (%s, %s) "
                "ON DUPLICATE KEY UPDATE image_face = VALUES(image_face)",
                (email, face_encoding.tobytes())
            )
            conn.commit()
            return jsonify({'status': 'success', 'message': 'Face registered successfully'})
        finally:
            conn.close()

    except Exception as e:
        logger.error(f"Registration error: {e}")
        return jsonify({'status': 'error', 'message': str(e)}), 500
@app.route('/compare_faces', methods=['POST'])
def compare_faces():
    """Compare uploaded face with stored face."""
    if 'email' not in request.form or 'uploaded_image' not in request.files:
        return jsonify({'status': 'error', 'message': 'Email and image are required'}), 400

    conn = None
    try:
        email = request.form['email']
        uploaded_image = request.files['uploaded_image']

        # Process uploaded image
        image = preprocess_image(uploaded_image)
        if image is None:
            return jsonify({'status': 'error', 'message': 'Invalid image format'}), 400

        # Get face encoding
        uploaded_encoding = get_face_encoding(image)
        if uploaded_encoding is None:
            return jsonify({'status': 'error', 'message': 'No face detected in uploaded image'}), 400

        # Get stored encoding from database
        conn = connect_database()
        if conn is None:
            return jsonify({'status': 'error', 'message': 'Database connection failed'}), 500

        cursor = conn.cursor()
        cursor.execute("SELECT image_face FROM pelanggan WHERE email = %s", (email,))
        result = cursor.fetchone()

        if not result or not result[0]:
            return jsonify({'status': 'error', 'message': 'No stored face found for this email'}), 404

        # Compare faces
        try:
            stored_encoding = np.frombuffer(result[0], dtype=np.float64)

            # Check if the length of the stored encoding is correct
            if stored_encoding.size != 128:  # Assuming face encodings are of size 128
                logger.error("Stored encoding size is incorrect.")
                return jsonify({
                    'status': 'success',
                    'match': False,
                    'confidence': 0.0,
                    'message': 'Face comparison failed due to invalid stored encoding size'
                })

            match, confidence = compare_encodings(stored_encoding, uploaded_encoding)

        except ValueError as e:
            logger.error(f"ValueError during face comparison: {e}")
            return jsonify({
                'status': 'success',
                'match': False,
                'confidence': 0.0,
                'message': 'Face comparison failed due to an internal error'
            })

        return jsonify({
            'status': 'success',
            'match': match,
            'confidence': confidence,
            'message': 'Face matched successfully' if match else 'Face did not match'
        })

    except Exception as e:
        logger.error(f"Face comparison error: {e}")
        return jsonify({'status': 'error', 'message': str(e)}), 500

    finally:
        if conn is not None:
            conn.close()

if __name__ == '__main__':
    app.run(debug=True)