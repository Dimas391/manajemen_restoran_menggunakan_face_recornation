import 'dart:async';
import 'dart:typed_data';
import 'package:path/path.dart' as p;
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:google_mlkit_face_detection/google_mlkit_face_detection.dart';
import 'package:sqflite/sqflite.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'SignUpPage.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';  // Import SpinKit

class FaceRecognitionScreen extends StatefulWidget {
  @override
  _FaceRecognitionScreenState createState() => _FaceRecognitionScreenState();
}

class _FaceRecognitionScreenState extends State<FaceRecognitionScreen> {
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

  // Database helper
  Database? _database;

  @override
  void initState() {
    super.initState();
    _initializeCamera();
    _initializeDatabase();
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
      onUpgrade: (db, oldVersion, newVersion) {
        if (oldVersion < 2) {
          db.execute('ALTER TABLE users ADD COLUMN image BLOB');
        }
      },
      version: 2,
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
        String name = "Detected Face";  // Bisa mengganti dengan nama pengguna yang valid jika sudah ada
        if (name.isNotEmpty) {
          int? userId = await _saveFaceToDatabase(name, _imageBytes!);
          if (userId != null) {
            await _uploadFaceToServer(_imageBytes!, userId);
          }
        } else {
          print("Data lainnya belum lengkap, menunggu data lainnya...");
        }

        // Arahkan ke halaman SignUp setelah gambar berhasil diambil
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

  Future<int?> _saveFaceToDatabase(String name, Uint8List imageBytes) async {
    int? userId;

    if (name.isNotEmpty) {
      userId = await _database?.insert(
        'users',
        {
          'name': name,  // Simpan nama pengguna (data lain) jika tersedia
          'image': imageBytes,  // Simpan gambar wajah
        },
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
      print("Face saved to database successfully with ID: $userId.");
    }

    return userId;
  }

 Future<void> _uploadFaceToServer(Uint8List imageBytes, int userId) async {
  final uri = Uri.parse('http://192.168.167.78/manajemen_resto/config/face.php');

  var request = http.MultipartRequest('POST', uri);
  request.files.add(http.MultipartFile.fromBytes('image_face', imageBytes, filename: 'face.jpg'));

  final Map<String, dynamic> jsonData = {
    'id_pelanggan': '44',  // Use the correct id_pelanggan here
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
    _cameraController?.dispose();
    _faceDetector.close();
    _database?.close();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Face Recornation')),
      body: _cameraController != null && _cameraController!.value.isInitialized
          ? Stack(
              children: [
                CameraPreview(_cameraController!),
                Center(
                  child: Container(
                    width: 300,
                    height: 300,
                    decoration: BoxDecoration(
                      border: Border.all(color: Colors.blueAccent),
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                ),
                if (_hasFaceDetected)
                  Center(
                    child: Icon(
                      Icons.check_circle,
                      color: Colors.green,
                      size: 100,
                    ),
                  )
                else
                  Center(
                    child: Icon(
                      Icons.cancel,
                      color: Colors.red,
                      size: 100,
                    ),
                  ),
                // Menampilkan animasi loading jika sedang mendeteksi wajah
                if (_isDetecting)
                  Center(
                    child: SpinKitFadingCircle(
                      color: Colors.blueAccent,
                      size: 50.0,
                    ),
                  ),
              ],
            )
          : const Center(child: CircularProgressIndicator()),
      floatingActionButton: FloatingActionButton(
        onPressed: _captureAndDetectFaces,
        tooltip: 'Capture & Detect',
        child: Icon(Icons.camera_alt),
      ),
    );
  }
}