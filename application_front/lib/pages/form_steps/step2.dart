import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:image_picker/image_picker.dart';
import 'package:permission_handler/permission_handler.dart';
import 'dart:io';

class StepIntervention extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController interventionController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;
  final Future<void> Function() saveChoices;
  final Map<String, bool> choices;
  final void Function(File image) setImage;

  StepIntervention({
    required this.formKey,
    required this.interventionController,
    required this.onNext,
    required this.onPrevious,
    required this.saveChoices,
    required this.choices,
    required this.setImage,
  });

  @override
  _StepInterventionState createState() => _StepInterventionState();
}

class _StepInterventionState extends State<StepIntervention> {
  late Map<String, bool> _choices;
  File? _image;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _choices = Map<String, bool>.from(widget.choices);
    _loadChoices();
    _loadImage();
  }

  Future<void> _loadChoices() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _choices.forEach((key, value) {
        _choices[key] = prefs.getBool(key) ?? false;
      });
    });
  }

  Future<void> _loadImage() async {
    final prefs = await SharedPreferences.getInstance();
    final imagePath = prefs.getString('repriseImagePath');
    if (imagePath != null) {
      setState(() {
        _image = File(imagePath);
      });
    }
  }

  Future<void> _requestPermissions() async {
    var status = await Permission.camera.status;
    if (!status.isGranted) {
      await Permission.camera.request();
    }
    status = await Permission.storage.status;
    if (!status.isGranted) {
      await Permission.storage.request();
    }
  }

  Future<void> _toggleChoice(String key) async {
    setState(() {
      _choices[key] = !_choices[key]!;
    });
    await widget.saveChoices();

    if (key == "Reprise matériel(s)" && _choices[key]!) {
      await _requestPermissions();
      await _pickImage();
    } else if (key == "Reprise matériel(s)" && !_choices[key]!) {
      setState(() {
        _image = null;
      });
      final prefs = await SharedPreferences.getInstance();
      prefs.remove('repriseImagePath');
    }
  }

  Future<void> _pickImage() async {
    try {
      final pickedFile = await _picker.pickImage(source: ImageSource.camera);
      if (pickedFile != null) {
        setState(() {
          _image = File(pickedFile.path);
        });
        final prefs = await SharedPreferences.getInstance();
        prefs.setString('repriseImagePath', _image!.path);
        widget.setImage(_image!);
      }
    } catch (e) {
      print('Error picking image: $e');
    }
  }

  bool _validateSelection() {
    return _choices.values.any((value) => value) &&
           (_choices["Reprise matériel(s)"]! ? _image != null : true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Type d'intervention"),
        automaticallyImplyLeading: false,
      ),
      body: Form(
        key: widget.formKey,
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                ..._choices.keys.map((String key) {
                  return CheckboxListTile(
                    title: Text(key),
                    value: _choices[key],
                    onChanged: (bool? value) async {
                      await _toggleChoice(key);
                    },
                  );
                }).toList(),
                if (_image != null) ...[
                  const SizedBox(height: 20),
                  Image.file(_image!),
                ],
                const SizedBox(height: 60),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: <Widget>[
                    Expanded(
                      child: ElevatedButton(
                        onPressed: widget.onPrevious,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.grey,
                        ),
                        child: const Text('Précédent'),
                      ),
                    ),
                    const SizedBox(width: 20),
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          if (_validateSelection()) {
                            widget.onNext();
                          } else {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text('Please take a picture for "Reprise matériel(s)".'),
                              ),
                            );
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