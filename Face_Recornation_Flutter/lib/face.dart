import 'dart:async';
import 'dart:typed_data';
import 'dart:ui';
import 'package:image/image.dart' as img;
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
  bool _isLoading = false;
  bool _isSuccess = false;
  Uint8List? _imageBytes;
  Database? _database;

  late AnimationController _scanAnimationController;
  late Animation<double> _scanAnimation;

  @override
  void initState() {
    super.initState();
    _initializeCamera();
    _initializeDatabase();

    _scanAnimationController = AnimationController(
      duration: const Duration(seconds: 10),
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

      if (faces.isNotEmpty) {
        // Start loading state
        setState(() {
          _isLoading = true;
          _isDetecting = false;
        });

        // Process the face detection
        Face face = faces[0];
        final boundingBox = face.boundingBox;
        final croppedFace = await _cropFace(imageBytes, boundingBox);

        String name = "Detected Face";
        if (name.isNotEmpty) {
          int? userId = await _saveFaceToDatabase(name, croppedFace);
          if (userId != null) {
            await _uploadFaceToServer(croppedFace, userId);
          }
        }

        // Show loading for 3 seconds
        await Future.delayed(Duration(seconds: 10));

        // Show success state
        setState(() {
          _isLoading = false;
          _isSuccess = true;
        });

        // Wait 2 seconds on success screen
        await Future.delayed(Duration(seconds: 5));

        // Navigate to SignUp page
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => SignUpPage()),
        );
      } else {
        setState(() {
          _isDetecting = false;
        });
        // Show error message if no face detected
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('No face detected. Please try again.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      print('Error detecting faces: $e');
      setState(() {
        _isDetecting = false;
        _isLoading = false;
      });
      // Show error message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error detecting face. Please try again.'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<Uint8List> _cropFace(Uint8List imageBytes, Rect boundingBox) async {
    img.Image originalImage = img.decodeImage(imageBytes)!;

    final left = boundingBox.left.toInt();
    final top = boundingBox.top.toInt();
    final width = (boundingBox.right - boundingBox.left).toInt();
    final height = (boundingBox.bottom - boundingBox.top).toInt();

    img.Image croppedImage = img.copyCrop(
      originalImage,
      x: left,
      y: top,
      width: width,
      height: height,
    );

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
      print("Face saved to database successfully with ID: $userId");
    }
    return userId;
  }

  Future<void> _uploadFaceToServer(Uint8List imageBytes, int userId) async {
    final uri = Uri.parse('http://192.168.233.78/manajemen_resto/config/face.php');

    var request = http.MultipartRequest('POST', uri);
    request.files.add(
      http.MultipartFile.fromBytes('image_face', imageBytes, filename: 'face.jpg'),
    );

    final Map<String, dynamic> jsonData = {
      'id_pelanggan': userId.toString(),
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
        print('Failed to upload image. Status code: ${response.statusCode}');
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

  Widget _buildCornerDecoration(Alignment alignment) {
    double? top = alignment.y < 0 ? -2 : null;
    double? bottom = alignment.y > 0 ? -2 : null;
    double? left = alignment.x < 0 ? -2 : null;
    double? right = alignment.x > 0 ? -2 : null;

    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: 30,
        height: 30,
        decoration: BoxDecoration(
          border: Border(
            top: alignment.y < 0 ? BorderSide(color: Colors.blue, width: 4) : BorderSide.none,
            bottom: alignment.y > 0 ? BorderSide(color: Colors.blue, width: 4) : BorderSide.none,
            left: alignment.x < 0 ? BorderSide(color: Colors.blue, width: 4) : BorderSide.none,
            right: alignment.x > 0 ? BorderSide(color: Colors.blue, width: 4) : BorderSide.none,
          ),
        ),
      ),
    );
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

                // Face frame (only show when not loading or success)
                if (!_isLoading && !_isSuccess)
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
                          _buildCornerDecoration(Alignment.topLeft),
                          _buildCornerDecoration(Alignment.topRight),
                          _buildCornerDecoration(Alignment.bottomLeft),
                          _buildCornerDecoration(Alignment.bottomRight),
                        ],
                      ),
                    ),
                  ),

                // Scanning animation (only show when detecting)
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

                // Loading or Success overlay
                if (_isLoading || _isSuccess)
                  Container(
                    color: Colors.black.withOpacity(0.7),
                    child: Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          if (_isLoading) ...[
                            SpinKitRing(
                              color: Colors.purple,
                              size: 100.0,
                              lineWidth: 5.0,
                            ),
                            SizedBox(height: 20),
                            Text(
                              'Processing...',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              'Please wait',
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.7),
                                fontSize: 16,
                              ),
                            ),
                          ] else if (_isSuccess) ...[
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
                              'Wajah Terdeteksi',
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
          : const Center(
              child: SpinKitRing(
                color: Colors.purple,
                size: 50.0,
                lineWidth: 3.0,
              ),
            ),
      floatingActionButton: !_isLoading && !_isSuccess
          ? Container(
              margin: EdgeInsets.only(bottom: 32),
              child: FloatingActionButton(
                onPressed: _captureAndDetectFaces,
                backgroundColor: Colors.purple,
                child: Icon(Icons.camera_alt, size: 32),
              ),
            )
          : null,
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }
}