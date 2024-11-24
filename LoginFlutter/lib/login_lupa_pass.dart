import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

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
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();
  bool _isNewPasswordVisible = false;
  bool _isConfirmPasswordVisible = false;
  bool _isLoading = false;
  final _formKey = GlobalKey<FormState>();

  Future<void> _resetPassword() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final response = await http.post(
        Uri.parse('http://192.168.81.78/manajemen_resto/config/update_password.php'),
        body: {
          'email': widget.email,
          'reset_token': widget.resetToken,
          'new_password': _newPasswordController.text,
        },
      );

      final responseData = json.decode(response.body);

      if (responseData['status'] == 'success') {
        showSuccessDialog('Password has been successfully reset.');
      } else {
        showErrorDialog(responseData['message'] ?? 'Failed to reset password.');
      }
    } catch (e) {
      showErrorDialog('An error occurred. Please try again later.');
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  void showSuccessDialog(String message) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Success'),
          content: Text(message),
          actions: [
            TextButton(
              child: const Text('OK'),
              onPressed: () {
                Navigator.of(context).pop(); // Close dialog
                Navigator.of(context).pushReplacementNamed('/login'); // Navigate to login
              },
            ),
          ],
        );
      },
    );
  }

  void showErrorDialog(String message) {
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF1F1530), Color(0xFF2E0E5C)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const SizedBox(height: 40),
                  Image.asset(
                    'image/burger.png',
                    height: 200,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(height: 40),
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Column(
                      children: [
                        const Text(
                          'Create New Password',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 20),
                        TextFormField(
                          controller: _newPasswordController,
                          obscureText: !_isNewPasswordVisible,
                          style: const TextStyle(color: Colors.white),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please enter a new password';
                            }
                            if (value.length < 6) {
                              return 'Password must be at least 6 characters';
                            }
                            return null;
                          },
                          decoration: InputDecoration(
                            labelText: 'New Password',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.lock, color: Colors.white),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _isNewPasswordVisible ? Icons.visibility : Icons.visibility_off,
                                color: Colors.white,
                              ),
                              onPressed: () {
                                setState(() {
                                  _isNewPasswordVisible = !_isNewPasswordVisible;
                                });
                              },
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.white),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.white),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.purple),
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),
                        TextFormField(
                          controller: _confirmPasswordController,
                          obscureText: !_isConfirmPasswordVisible,
                          style: const TextStyle(color: Colors.white),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please confirm your password';
                            }
                            if (value != _newPasswordController.text) {
                              return 'Passwords do not match';
                            }
                            return null;
                          },
                          decoration: InputDecoration(
                            labelText: 'Confirm Password',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.lock, color: Colors.white),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _isConfirmPasswordVisible ? Icons.visibility : Icons.visibility_off,
                                color: Colors.white,
                              ),
                              onPressed: () {
                                setState(() {
                                  _isConfirmPasswordVisible = !_isConfirmPasswordVisible;
                                });
                              },
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.white),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.white),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: Colors.purple),
                            ),
                          ),
                        ),
                        const SizedBox(height: 30),
                        SizedBox(
                          width: double.infinity,
                          height: 50,
                          child: ElevatedButton(
                            onPressed: _isLoading ? null : _resetPassword,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.purple,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                            child: _isLoading
                                ? const CircularProgressIndicator(color: Colors.white)
                                : const Text(
                                    'Reset Password',
                                    style: TextStyle(fontSize: 18),
                                  ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }
}