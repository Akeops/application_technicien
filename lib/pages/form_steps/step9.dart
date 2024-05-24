import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:signature/signature.dart';

class SignatoryDocumentPage extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final VoidCallback onPrevious;
  final Future<bool> Function() uploadData;
  final SignatureController signatureController;
  final Function(File) setImage;

  SignatoryDocumentPage({
    super.key,
    required this.formKey,
    required this.onPrevious,
    required this.uploadData,
    required this.signatureController,
    required this.setImage, required Future<void> Function() pickImage, File? image,
  });

  @override
  _SignatoryDocumentPageState createState() => _SignatoryDocumentPageState();
}

class _SignatoryDocumentPageState extends State<SignatoryDocumentPage> {
  File? _image;
  final ImagePicker _picker = ImagePicker();

  Future<void> _getImage() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.camera);
    if (pickedFile != null) {
      setState(() {
        _image = File(pickedFile.path);
        widget.setImage(_image!);  // Set the image in the parent state
      });
    }
  }

  Future<void> _submitForm() async {
    // Ensure image is taken
    if (_image == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("S'il vous plaît, prenez une photo")),
      );
      return;
    }

    // Ensure signature is captured
    final Uint8List? signatureData = await widget.signatureController.toPngBytes();
    if (signatureData == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Veuillez fournir une signature')),
      );
      return;
    }

    // Debugging prints
    print("Signature data captured successfully");

    // Pass the image and signature data to the upload function
    if (await widget.uploadData()) {
      Navigator.pushReplacementNamed(context, '/homepage');  // Navigate on success
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Echec lors de l'envoi du formulaire."))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Photo and Signature"),
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
                  child: const Text('Prendre une photo'),
                ),
                const SizedBox(height: 20),
                Signature(
                  controller: widget.signatureController,
                  height: 200,
                  backgroundColor: Colors.grey[200]!,
                ),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => widget.signatureController.clear(), 
                  child: const Text('Nouvelle signature'),
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
                        child: const Text('Previous'),
                      ),
                    ),
                    const SizedBox(width: 20),  // Add spacing between the buttons
                    SizedBox(
                      width: 150,
                      child: ElevatedButton(
                        onPressed: _submitForm,
                        child: const Text('Envoyer'),
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