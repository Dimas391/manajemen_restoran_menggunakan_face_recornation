import 'dart:async';
import 'dart:typed_data';
import 'dart:ui';
import 'package:image/image.dart' as img; // Import the image package
import 'package:path/path.dart' as p;
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:google_mlkit_face_detection/google_mlkit_face_detection.dart';
import 'package:sqflite/sqflite.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:flutter_spinkit/flutter_spinkit.dart';
import 'SignUpPage.dart';

class FaceRecognitionScreen extends StatefulWidget {
  @override
  _FaceRecognitionScreenState createState() => _FaceRecognitionScreenState();
}

class _FaceRecognitionScreenState extends State<FaceRecognitionScreen>
    with SingleTickerProviderStateMixin {
  CameraController? _cameraController;
  late Future<void> _initializeControllerFuture;
  final FaceDetector _faceDetector = FaceDetector(
    options: FaceDetectorOptions(
      enableContours: true,
      enableClassification: true,
      minFaceSize: 0.1,
    ),
  );
  bool _isDetecting = false;
  bool _hasFaceDetected = false;
  Uint8List? _imageBytes;
  Database? _database;

  // Animation controller for scanning effect
  late AnimationController _scanAnimationController;
  late Animation<double> _scanAnimation;

  @override
  void initState() {
    super.initState();
    _initializeCamera();
    _initializeDatabase();

    // Initialize scanning animation
    _scanAnimationController = AnimationController(
      duration: const Duration(seconds: 2),
      vsync: this,
    )..repeat();

    _scanAnimation = Tween<double>(begin: -1.0, end: 1.0).animate(_scanAnimationController);
  }

  Future<void> _initializeCamera() async {
    try {
      final cameras = await availableCameras();
      CameraDescription? frontCamera;
      for (var camera in cameras) {
        if (camera.lensDirection == CameraLensDirection.front) {
          frontCamera = camera;
          break;
        }
      }

      if (frontCamera != null) {
        _cameraController = CameraController(
          frontCamera,
          ResolutionPreset.high,
        );
        _initializeControllerFuture = _cameraController!.initialize();
        setState(() {});
      } else {
        print("No front camera available");
      }
    } catch (e) {
      print('Error initializing camera: $e');
    }
  }

  Future<void> _initializeDatabase() async {
    _database = await openDatabase(
      p.join(await getDatabasesPath(), 'face_recognition.db'),
      onCreate: (db, version) {
        return db.execute(
          'CREATE TABLE users(id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, image BLOB)',
        );
      },
      version: 1,
    );
  }

  Future<void> _captureAndDetectFaces() async {
    if (_cameraController == null || _isDetecting) return;

    setState(() {
      _isDetecting = true;
    });

    try {
      XFile picture = await _cameraController!.takePicture();
      Uint8List imageBytes = await picture.readAsBytes();
            _imageBytes = imageBytes;

      final inputImage = InputImage.fromFilePath(picture.path);
      final List<Face> faces = await _faceDetector.processImage(inputImage);

      setState(() {
        _hasFaceDetected = faces.isNotEmpty;
      });

      if (_hasFaceDetected) {
        // Assuming we take the first detected face
        Face face = faces[0];
        final boundingBox = face.boundingBox;

        // Crop the image to the bounding box of the detected face
        final croppedFace = await _cropFace(imageBytes, boundingBox);

        String name = "Detected Face";
        if (name.isNotEmpty) {
          int? userId = await _saveFaceToDatabase(name, croppedFace);
          if (userId != null) {
            await _uploadFaceToServer(croppedFace, userId);
          }
        }

        // Wait for 2 seconds to show the success screen
        await Future.delayed(Duration(seconds: 2));

        // Navigate to SignUp page
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => SignUpPage()),
        );
      }
    } catch (e) {
      print('Error detecting faces: $e');
    } finally {
      setState(() {
        _isDetecting = false;
      });
    }
  }

  // Method to crop the face from the image
 // Method to crop the face from the image
// Method to crop the face from the image
Future<Uint8List> _cropFace(Uint8List imageBytes, Rect boundingBox) async {
  // Decode the image using the image package
  img.Image originalImage = img.decodeImage(imageBytes)!;

  // Calculate the cropping area
  final left = boundingBox.left.toInt();
  final top = boundingBox.top.toInt();
  final width = (boundingBox.right - boundingBox.left).toInt();
  final height = (boundingBox.bottom - boundingBox.top).toInt();

  // Crop the image
  img.Image croppedImage = img.copyCrop(originalImage, x: left, y: top, width: width, height: height);

  // Encode the cropped image to bytes
  return Uint8List.fromList(img.encodePng(croppedImage));
}

  Future<int?> _saveFaceToDatabase(String name, Uint8List imageBytes) async {
    int? userId;
    if (name.isNotEmpty) {
      userId = await _database?.insert(
        'users',
        {
          'name': name,
          'image': imageBytes,
        },
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
      print("Face saved to database successfully with ID: $userId.");
    }
    return userId;
  }

  Future<void> _uploadFaceToServer(Uint8List imageBytes, int userId) async {
    final uri = Uri.parse('http://192.168.81.78/manajemen_resto/config/face.php');

    var request = http.MultipartRequest('POST', uri);
    request.files.add(
      http.MultipartFile.fromBytes('image_face', imageBytes, filename: 'face.jpg'),
    );

    final Map<String, dynamic> jsonData = {
      'id_pelanggan': userId.toString(), // Use the actual user ID
    };

    request.fields['json_data'] = json.encode(jsonData);

    try {
      var response = await request.send();
      if (response.statusCode == 200) {
        var responseBody = await response.stream.bytesToString();
        print('Image uploaded successfully. Response: $responseBody');

        var decodedResponse = json.decode(responseBody);
        if (decodedResponse['status'] == 'success') {
          print("Upload successful: ${decodedResponse['message']}");
        } else {
          print("Server error: ${decodedResponse['message']}");
        }
      } else {
        print('Failed to upload image. HTTP status code: ${response.statusCode}');
      }
    } catch (e) {
      print('Error uploading image: $e');
    }
  }

  @override
  void dispose() {
    _scanAnimationController.dispose();
    _cameraController?.dispose();
    _faceDetector.close();
    _database?.close();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: _cameraController != null && _cameraController!.value.isInitialized
          ? Stack(
              fit: StackFit.expand,
              children: [
                // Camera Preview
                Transform.scale(
                  scale: 1.0,
                  child: Center(
                    child: CameraPreview(_cameraController!),
                  ),
                ),
                
                // Dark overlay
                Container(
                  color: Colors.black.withOpacity(0.5),
                               ),
                
                // Face frame
                Center(
                  child: Container(
                    width: 280,
                    height: 280,
                    decoration: BoxDecoration(
                      border: Border.all(color: Colors.white.withOpacity(0.3)),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Stack(
                      children: [
                        // Top-left corner
                        Positioned(
                          top: -2,
                          left: -2,
                          child: Container(
                            width: 30,
                            height: 30,
                            decoration: BoxDecoration(
                              border: Border(
                                top: BorderSide(color: Colors.blue, width: 4),
                                left: BorderSide(color: Colors.blue, width: 4),
                              ),
                            ),
                          ),
                        ),
                        // Top-right corner
                        Positioned(
                          top: -2,
                          right: -2,
                          child: Container(
                            width: 30,
                            height: 30,
                            decoration: BoxDecoration(
                              border: Border(
                                top: BorderSide(color: Colors.blue, width: 4),
                                right: BorderSide(color: Colors.blue, width: 4),
                              ),
                            ),
                          ),
                        ),
                        // Bottom-left corner
                        Positioned(
                          bottom: -2,
                          left: -2,
                          child: Container(
                            width: 30,
                            height: 30,
                            decoration: BoxDecoration(
                              border: Border(
                                bottom: BorderSide(color: Colors.blue, width: 4),
                                left: BorderSide(color: Colors.blue, width: 4),
                              ),
                            ),
                          ),
                        ),
                        // Bottom-right corner
                        Positioned(
                          bottom: -2,
                          right: -2,
                          child: Container(
                            width: 30,
                            height: 30,
                            decoration: BoxDecoration(
                              border: Border(
                                bottom: BorderSide(color: Colors.blue, width: 4),
                                right: BorderSide(color: Colors.blue, width: 4),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                
                // Scanning animation
                if (_isDetecting)
                  Center(
                    child: Container(
                      width: 280,
                      height: 280,
                      child: AnimatedBuilder(
                        animation: _scanAnimation,
                        builder: (context, child) {
                          return Align(
                            alignment: Alignment(0, _scanAnimation.value),
                            child: Container(
                              height: 2,
                              width: 280,
                              color: Colors.purple.withOpacity(0.5),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                
                // Success overlay
                if (_hasFaceDetected)
                  Container(
                    color: Colors.black.withOpacity(0.7),
                    child: Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            width: 100,
                            height: 100,
                            decoration: BoxDecoration(
                              color: Colors.purple,
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              Icons.check,
                              color: Colors.white,
                              size: 60,
                            ),
                          ),
                          SizedBox(height: 20),
                          Text(
                            'Scan Completed',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            'Thank you',
                            style: TextStyle(
                              color: Colors.white.withOpacity(0.7),
                              fontSize: 16,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                
                // Back button
                Positioned(
                  top: 40,
                  left: 16,
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.5),
                      shape: BoxShape.circle,
                    ),
                    child: IconButton(
                      icon: Icon(Icons.arrow_back, color: Colors.white),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ),
                ),
              ],
            )
          : const Center(child: CircularProgressIndicator()),
      
      // Camera button
      floatingActionButton: Container(
        margin: EdgeInsets.only(bottom: 32),
        child: FloatingActionButton(
          onPressed: _captureAndDetectFaces,
          backgroundColor: Colors.purple,
          child: Icon(Icons.camera_alt, size: 32),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }
}