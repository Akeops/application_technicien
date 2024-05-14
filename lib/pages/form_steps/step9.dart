import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
//import 'package:shared_preferences/shared_preferences.dart';
import 'package:signature/signature.dart';

class SignatoryDocumentPage extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const SignatoryDocumentPage({
    super.key,
    required this.formKey,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _SignatoryDocumentPageState createState() => _SignatoryDocumentPageState();
}

class _SignatoryDocumentPageState extends State<SignatoryDocumentPage> {
  final SignatureController _signatureController = SignatureController(
    penStrokeWidth: 5,
    penColor: Colors.black,
  );
  File? _image;
  final ImagePicker _picker = ImagePicker();

  Future<void> _getImage() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.camera);
    if (pickedFile != null) {
      setState(() {
        _image = File(pickedFile.path);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Upload Document and Signature"),
        automaticallyImplyLeading: false,
      ),
      body: GestureDetector(
        behavior: HitTestBehavior.opaque,
        onTap: () => FocusScope.of(context).unfocus(),
        child: Form(
          key: widget.formKey,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                if (_image != null) Image.file(_image!),
                ElevatedButton(
                  onPressed: _getImage,
                  child: const Text('Take Picture'),
                ),
                const SizedBox(height: 20),
                Signature(
                  controller: _signatureController,
                  height: 200,
                  backgroundColor: Colors.grey[200]!,
                ),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => _signatureController.clear(),
                  child: const Text('Clear Signature'),
                ),
                const SizedBox(height: 40),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: <Widget>[
                    SizedBox(
                      width: 150,
                      child: ElevatedButton(
                        onPressed: widget.onPrevious,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.grey,
                        ),
                        child: const Text('Précédent'),
                      ),
                    ),
                    SizedBox(width: 20),  // Add spacing between the buttons
                    SizedBox(
                      width: 150,
                      child: ElevatedButton(
                        onPressed: () {
                          if (widget.formKey.currentState!.validate()) {
                            widget.onNext();
                          }
                        },
                        child: const Text('Suivant'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}