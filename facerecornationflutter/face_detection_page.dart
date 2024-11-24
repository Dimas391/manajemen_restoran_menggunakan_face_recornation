// face_detection_page.dart
import 'dart:async';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:google_ml_kit/google_ml_kit.dart';
import 'package:google_mlkit_face_detection/google_mlkit_face_detection.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class FaceDetectionPage extends StatefulWidget {
  final String email;

  FaceDetectionPage({required this.email});

  @override
  _FaceDetectionPageState createState() => _FaceDetectionPageState();
}

class _FaceDetectionPageState extends State<FaceDetectionPage> {
  bool _isLoading = false;
  bool _isFaceRecognitionStep = false;
  CameraController? _cameraController;
  FaceDetector? _faceDetector;

  static const String verifyFaceUrl = 'http://192.168.60.78/manajemen_resto/config/verify_face.php';

  @override
  void initState() {
    super.initState();
    _faceDetector = GoogleMlKit.vision.faceDetector(
      FaceDetectorOptions(
        enableClassification: true,
        minFaceSize: 0.15,
      ),
    );
    _startFaceRecognition();
  }

  Future<void> _startFaceRecognition() async {
    final cameras = await availableCameras();
    if (cameras.isEmpty) {
      _showDialog('No camera available');
      return;
    }

    _cameraController = CameraController(
      cameras.firstWhere((camera) => camera.lensDirection == CameraLensDirection.front),
      ResolutionPreset.high,
    );

    try {
      await _cameraController!.initialize();
      setState(() {
        _isFaceRecognitionStep = true;
      });
    } catch (e) {
      _showDialog('Error initializing camera: $e');
    }
  }

  Future<void> _captureFace() async {
    if (_cameraController == null || !_cameraController!.value.isInitialized) {
      _showDialog('Camera not initialized');
      return;
    }

    try {
      final image = await _cameraController!.takePicture();
      final inputImage = InputImage.fromFilePath(image.path);
      
            final List<Face> faces = await _faceDetector!.processImage(inputImage);

      if (faces.isEmpty) {
        _showDialog('No face detected. Please try again.');
        return;
      }

      await _verifyFaceWithServer(image.path);
    } catch (e) {
      _showDialog('Error in face detection: $e');
    }
  }

  Future<void> _verifyFaceWithServer(String imagePath) async {
    setState(() {
      _isLoading = true;
    });

    final url = Uri.parse(verifyFaceUrl);
    
    try {
      var request = http.MultipartRequest('POST', url);
      request.fields['email'] = widget.email; // Use the email passed from the previous page
      request.files.add(await http.MultipartFile.fromPath('face_image', imagePath));

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();
      final data = json.decode(responseBody);

      setState(() {
        _isLoading = false;
      });

      if (response.statusCode == 200 && data['status'] == 'success') {
        _showDialog('Face verified successfully. Reset password link sent to your email.');
        _resetCameraAndForm();
      } else {
        _showDialog('Face verification failed: ${data['message']}');
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      _showDialog('Error verifying face: $e');
    }
  }

  void _resetCameraAndForm() {
    _cameraController?.dispose();
    Navigator.pop(context); // Go back to the previous page
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Background Gradient
          Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF1F1530), Color(0xFF2E0E5C)],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
            ),
          ),

          // Main Content
          SingleChildScrollView(
            padding: EdgeInsets.symmetric(horizontal: 20, vertical: 40),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (_isFaceRecognitionStep && _cameraController != null)
                  Column(
                    children: [
                      AspectRatio(
                        aspectRatio: _cameraController!.value.aspectRatio,
                        child: CameraPreview(_cameraController!),
                      ),
                      SizedBox(height: 20),
                      Text(
                        'Position your face in the camera',
                        style: TextStyle(color: Colors.white, fontSize: 18),
                      ),
                      SizedBox(height: 20),
                      ElevatedButton(
                        onPressed: _captureFace,
                        child: Text('Capture Face'),
                      ),
                      if (_isLoading) CircularProgressIndicator(color: Colors.white),
                    ],
                  )
                else
                  Center(
                    child: CircularProgressIndicator(color: Colors.white),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _showDialog(String message) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Face Detection'),
          content: Text(message),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: Text('OK'),
            ),
          ],
        );
      },
    );
  }

  @override
  void dispose() {
    _cameraController?.dispose();
    _faceDetector?.close();
    super.dispose();
  }
}