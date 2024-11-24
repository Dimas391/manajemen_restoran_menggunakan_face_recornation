import 'dart:async';
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:google_ml_kit/google_ml_kit.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:image/image.dart' as img;
import 'login_lupa_pass.dart';
import 'SignUpPage.dart';

class ForgotPasswordPage extends StatefulWidget {
  @override
  _ForgotPasswordPageState createState() => _ForgotPasswordPageState();
}

class _ForgotPasswordPageState extends State<ForgotPasswordPage>
    with SingleTickerProviderStateMixin {
  final TextEditingController _emailController = TextEditingController();
  bool _isLoading = false;
  bool _isFaceRecognitionStep = false;
  bool _isDetecting = false;
  bool _hasFaceDetected = false;
  CameraController? _cameraController;
  FaceDetector? _faceDetector;

  // Variabel untuk menyimpan token reset
  String? _resetToken;

  // Animation controller for scanning effect
  late AnimationController _scanAnimationController;
  late Animation<double> _scanAnimation;

  static const String validateEmailUrl = 'http://192.168.81.78/manajemen_resto/config/validate_email.php';
  static const String verifyFaceUrl = 'http://192.168.81.78/manajemen_resto/config/verify_face.php';

  @override
  void initState() {
    super.initState();
    _faceDetector = GoogleMlKit.vision.faceDetector(
      FaceDetectorOptions(
        enableClassification: true,
        minFaceSize: 0.15,
      ),
    );

    // Initialize scanning animation
    _scanAnimationController = AnimationController(
      duration: const Duration(seconds: 2),
      vsync: this,
    )..repeat();

    _scanAnimation = Tween<double>(begin: -1.0, end: 1.0).animate(_scanAnimationController);
  }

  Future<void> _navigateToPasswordReset(String email, String resetToken) {
    return Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => PasswordResetFlow(
          email: email,
          resetToken: resetToken,
        ),
      ),
    );
  }

 Future<void> _verifyFaceWithServer(String imagePath) async {
    setState(() {
        _isLoading = true;
    });

    final url = Uri.parse(verifyFaceUrl);

    try {
        var request = http.MultipartRequest('POST', url);
        request.fields['email'] = _emailController.text.trim();
        request.files.add(await http.MultipartFile.fromPath('face_image', imagePath));

        final response = await request.send();
        final responseBody = await response.stream.bytesToString();
        final data = json.decode(responseBody);

        // Check the response status
        if (response.statusCode == 200) {
            if (data['status'] == 'success') {
                setState(() {
                    _hasFaceDetected = true;
                });

                // Wait for 2 seconds to show success animation
                await Future.delayed(Duration(seconds: 2));

                // Navigate to password reset page
                if (data['reset_token'] != null) {
                    _resetToken = data['reset_token'];
                    await _navigateToPasswordReset(_emailController.text.trim(), _resetToken!);
                    _resetCameraAndForm();
                } else {
                    _showDialog('Error: Reset token not received from server');
                }
            } else {
                // Redirect to reset password page on error
                _showDialog('Face verification failed: ${data['message']}');
                await _navigateToPasswordReset(_emailController.text.trim(), '');
            }
        } else {
            _showDialog('Error: Unable to reach the server.');
        }
    } catch (e) {
        _showDialog('Error verifying face: $e');
    } finally {
        setState(() {
            _isLoading = false;
        });
    }
}

  Future<void> _resetPassword() async {
    String email = _emailController.text.trim();

    if (email.isEmpty) {
      _showDialog('Please enter your email address.');
      return;
    }

    final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
    if (!emailRegex.hasMatch(email)) {
      _showDialog('Please enter a valid email address.');
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final response = await http.post(
        Uri.parse(validateEmailUrl),
        body: jsonEncode({'email': email}),
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json"
        },
      );

            if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          await _startFaceRecognition();
        } else {
          _showDialog('Email not found: ${data['message']}');
        }
      } else {
        _showDialog('Error: Unable to reach the server.');
      }
    } catch (e) {
      _showDialog('An error occurred while validating email: ${e.toString()}');
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _startFaceRecognition() async {
    final cameras = await availableCameras();
    CameraDescription? frontCamera;

    for (var camera in cameras) {
      if (camera.lensDirection == CameraLensDirection.front) {
        frontCamera = camera;
        break;
      }
    }

    if (frontCamera == null) {
      _showDialog('No front camera available');
      return;
    }

    _cameraController = CameraController(
      frontCamera,
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
    if (_cameraController == null ||
        !_cameraController!.value.isInitialized ||
        _isDetecting) {
      return;
    }

    setState(() {
      _isDetecting = true;
    });

    try {
      final image = await _cameraController!.takePicture();
      final inputImage = InputImage.fromFilePath(image.path);
      final List<Face> faces = await _faceDetector!.processImage(inputImage);

      setState(() {
        _hasFaceDetected = faces.isNotEmpty;
      });

      if (faces.isEmpty) {
        _showDialog('No face detected. Please try again.');
        setState(() {
          _isDetecting = false;
        });
        return;
      }

      // Load the image bytes
      final imageBytes = await File(image.path).readAsBytes();

      // Crop the face from the image
      final croppedImageBytes = await _cropFace(imageBytes, faces[0].boundingBox);

      // Save the cropped image to a temporary file
      final croppedImagePath = image.path.replaceAll('.jpg', '_cropped.png');
      await File(croppedImagePath).writeAsBytes(croppedImageBytes);

      // Verify the cropped face with the server
      await _verifyFaceWithServer(croppedImagePath);

      // Wait for 2 seconds to show the success screen
      await Future.delayed(Duration(seconds: 2));

    } catch (e) {
      _showDialog('Error in face detection: $e');
    } finally {
      setState(() {
        _isDetecting = false;
      });
    }
  }

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

  void _resetCameraAndForm() {
    _cameraController?.dispose();
    setState(() {
      _isFaceRecognitionStep = false;
      _hasFaceDetected = false;
      _emailController.clear();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _isFaceRecognitionStep
          ? _buildFaceRecognitionUI()
          : _buildEmailFormUI(),
    );
  }

  Widget _buildFaceRecognitionUI() {
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
                            'Face Verified',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            'Processing...',
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
                      onPressed: () {
                        setState(() {
                          _isFaceRecognitionStep = false;
                          _hasFaceDetected = false;
                        });
                      },
                    ),
                  ),
                ),
              ],
            )
          : const Center(child: CircularProgressIndicator()),
      floatingActionButton: Container(
        margin: EdgeInsets.only(bottom: 32),
        child: FloatingActionButton(
          onPressed: _captureFace,
          backgroundColor: Colors.purple,
          child: Icon(Icons.camera_alt, size: 32),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }

  Widget _buildEmailFormUI() {
    return Stack(
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
              // Logo/Image
              Padding(
                padding: const EdgeInsets.only(bottom: 20),
                child: Image.asset(
                  'image/burger.png',
                  height: 200,
                  fit: BoxFit.contain,
                ),
              ),

              // Form Container
              ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: Container(
                  padding: EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Column(
                    children: [
                      Text(
                        'Forgot Password',
                        style: TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      SizedBox(height: 20),

                      Text(
                        'Enter your email address to reset your password.',
                        style: TextStyle(fontSize: 16, color: Colors.white70),
                        textAlign: TextAlign.center,
                      ),
                      SizedBox(height: 20),

                      _buildTextField(),
                      SizedBox(height: 20),

                      _isLoading
                          ? CircularProgressIndicator(color: Colors.white)
                          : _buildResetPasswordButton(),
                    ],
                  ),
                               ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTextField() {
    return TextField(
      controller: _emailController,
      decoration: InputDecoration(
        labelText: 'Email Address',
        labelStyle: TextStyle(color: Colors.white),
        enabledBorder: OutlineInputBorder(
          borderSide: BorderSide(color: Colors.white),
        ),
        focusedBorder: OutlineInputBorder(
          borderSide: BorderSide(color: Colors.purple),
        ),
        filled: true,
        fillColor: Colors.white.withOpacity(0.2),
      ),
      style: TextStyle(color: Colors.white),
      keyboardType: TextInputType.emailAddress,
    );
  }

  Widget _buildResetPasswordButton() {
    return ElevatedButton(
      onPressed: _resetPassword,
      style: ElevatedButton.styleFrom(
        backgroundColor: Colors.purple,
        padding: EdgeInsets.symmetric(vertical: 15),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
      ),
      child: Text(
        'Reset Password',
        style: TextStyle(fontSize: 18),
      ),
    );
  }

  void _showDialog(String message) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Notification'),
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
    _scanAnimationController.dispose();
    super.dispose();
  }
}

class PasswordResetFlow extends StatefulWidget {
  final String email;
  final String resetToken;

  const PasswordResetFlow({
    Key? key,
    required this.email,
    required this.resetToken,
  }) : super(key: key);

  @override
  _PasswordResetFlowState createState() => _PasswordResetFlowState();
}

class _PasswordResetFlowState extends State<PasswordResetFlow> {
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  Future<void> _updatePassword() async {
    if (_passwordController.text.isEmpty || _confirmPasswordController.text.isEmpty) {
      _showErrorDialog('Please fill in all fields');
      return;
    }

    if (_passwordController.text != _confirmPasswordController.text) {
      _showErrorDialog('Passwords do not match');
      return;
    }

    // Add password validation
    if (_passwordController.text.length < 8) {
      _showErrorDialog('Password must be at least 8 characters long');
      return;
    }

    setState(() => _isLoading = true);

    try {
      final response = await http.post(
        Uri.parse('http://192.168.81.78/manajemen_resto/config/update_password.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': widget.email,
          'reset_token': widget.resetToken, // Added reset token
          'new_password': _passwordController.text,
        }),
      );

      final data = jsonDecode(response.body);

      if (data['status'] == 'success') {
        await _showSuccessDialog();
        if (mounted) {
          Navigator.of(context).pushAndRemoveUntil(
            MaterialPageRoute(builder: (context) => SignUpPage()),
            (route) => false,
          );
        }
      } else {
        _showErrorDialog(data['message'] ?? 'Failed to update password');
      }
    } catch (e) {
      _showErrorDialog('An error occurred: ${e.toString()}');
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Colors.black,
              Color(0xFF2E0E5C),
            ],
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildHeader(),
                _buildBurgerImage(),
                _buildFormContainer(),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Row(
      children: const [
        Icon(
          Icons.restaurant_menu,
          color: Colors.purple,
          size: 30,
        ),
        SizedBox(width: 8),
        Text(
          'Restoran',
          style: TextStyle(
            color: Colors.purple,
            fontSize: 24,
            fontWeight: FontWeight.bold,
          ),
        ),
      ],
    );
  }

  Widget _buildBurgerImage() {
    return Container(
      height: 200,
      margin: const EdgeInsets.symmetric(vertical: 24),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        image: const DecorationImage(
          image: AssetImage('image/burger.png'),
          fit: BoxFit.cover,
        ),
      ),
    );
  }

  Widget _buildFormContainer() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.black.withOpacity(0.3),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: Colors.white.withOpacity(0.1),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Center(
            child: Text(
              'Create New Password',
              style: TextStyle(
                color: Colors.white,
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          const Center(
            child: Text(
              'Autentifikasi 2 Langkah',
              style: TextStyle(
                color: Colors.grey,
                fontSize: 14,
              ),
            ),
          ),
          const SizedBox(height: 32),
          _buildPasswordField(
            controller: _passwordController,
            label: 'Password',
            // style: TextStyle(
            //   color: Colors.white
            // ),
            obscureText: _obscurePassword,
            onToggle: () => setState(() => _obscurePassword = !_obscurePassword),
          ),
          const SizedBox(height: 16),
          _buildPasswordField(
            controller: _confirmPasswordController,
            label: 'Konfirmasi Password',
            obscureText: _obscureConfirmPassword,
            onToggle: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
          ),
          const SizedBox(height: 32),
          _buildSubmitButton(),
          _buildDivider(),
          _buildSocialButtons(),
        ],
      ),
    );
  }

  Widget _buildSubmitButton() {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: ElevatedButton(
        onPressed: _isLoading ? null : _updatePassword,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.purple,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
        child: _isLoading
            ? const CircularProgressIndicator(color: Colors.white)
            : const Text(
                'Update Password',
                style: TextStyle(
                  color: Colors.grey,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
      ),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 24),
      child: Row(
        children: [
          const Expanded(child: Divider(color: Colors.grey)),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Text(
              'Or sign up with',
              style: TextStyle(
                color: Colors.grey[400],
                fontSize: 14,
              ),
            ),
          ),
          const Expanded(child: Divider(color: Colors.grey)),
        ],
      ),
    );
  }

  Widget _buildSocialButtons() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        _buildGoogleButton(),
        _buildSocialIconButton(Icons.apple, Colors.white),
        _buildSocialIconButton(Icons.facebook, Colors.blue),
      ],
    );
  }

  Widget _buildGoogleButton() {
    return CircleAvatar(
      backgroundColor: Colors.white12,
      child: Image.asset(
        'image/Buttons.png',
        height: 74,
        width: 74,
      ),
    );
  }

  Widget _buildSocialIconButton(IconData icon, Color color) {
    return Container(
      width: 50,
      height: 50,
      decoration: BoxDecoration(
        color: Colors.black.withOpacity(0.3),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Colors.grey[700]!,
          width: 1,
        ),
      ),
      child: Icon(icon, color: color),
    );
  }

  Widget _buildPasswordField({
    required TextEditingController controller,
    required String label,
    required bool obscureText,
    required VoidCallback onToggle,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            color: Colors.grey[400],
            fontSize: 14,
          ),
        ),
        const SizedBox(height: 8),
        Container(
          decoration: BoxDecoration(
            color: Colors.black.withOpacity(0.3),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: Colors.grey[700]!,
              width: 1,
            ),
          ),
          child: TextField(
            controller: controller,
            obscureText: obscureText,
            style: const TextStyle(color: Colors.white),
            decoration: InputDecoration(
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              suffixIcon: IconButton(
                icon: Icon(
                  obscureText ? Icons.visibility : Icons.visibility_off,
                  color: Colors.grey[400],
                ),
                onPressed: onToggle,
              ),
            ),
          ),
        ),
      ],
    );
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Error'),
          content: Text(message),
          actions: [
            TextButton(
              child: const Text('OK'),
              onPressed: () => Navigator.of(context).pop(),
            ),
          ],
        );
      },
    );
  }

  Future<void> _showSuccessDialog() {
    return showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Success'),
          content: const Text('Your password has been updated successfully.'),
          actions: [
            TextButton(
              child: const Text('OK'),
              onPressed: () => Navigator.of(context).pop(),
            ),
          ],
        );
      },
    );
  }

  @override
  void dispose() {
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }
}
