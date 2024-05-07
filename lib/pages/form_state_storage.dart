import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';

class myFormState with ChangeNotifier {
  SharedPreferences? _preferences;
  int _currentStep = 0;
  Map<String, dynamic> _formData = {};

  // Define controllers
  TextEditingController dateController = TextEditingController(); // First page
  TextEditingController emailController = TextEditingController(); // Second page
  TextEditingController ageController = TextEditingController(); // Third page
  TextEditingController firstAgeController = TextEditingController();
  TextEditingController secondAgeController = TextEditingController();
  TextEditingController thirdAgeController = TextEditingController();
  TextEditingController fourthAgeController = TextEditingController();
  TextEditingController fifthAgeController = TextEditingController();
  TextEditingController sixAgeController = TextEditingController();
  TextEditingController sevenAgeController = TextEditingController();
  TextEditingController eightAgeController = TextEditingController();
  TextEditingController nineAgeController = TextEditingController();
  // Define other controllers as needed...

  int get currentStep => _currentStep;
  Map<String, dynamic> get formData => _formData;

  myFormState() {
    _loadFromPrefs();
    dateController.text = DateFormat('dd-MM-yyyy').format(DateTime.now());
  }

  void _loadFromPrefs() async {
    try {
      _preferences = await SharedPreferences.getInstance();
      _currentStep = _preferences?.getInt('form_step') ?? 0;
      String? formDataJson = _preferences?.getString('form_data');
      if (formDataJson != null) {
        _formData = json.decode(formDataJson);
        // Update controllers if their corresponding data exists
        firstAgeController.text = _formData['firstAge'] ?? '';
        secondAgeController.text = _formData['secondAge'] ?? '';
        thirdAgeController.text = _formData['thirdAge'] ?? '';
        fourthAgeController.text = _formData['fourthAge'] ?? '';
        fifthAgeController.text = _formData['fifthAge'] ?? '';
        sixAgeController.text = _formData['sixAge'] ?? '';
        sevenAgeController.text = _formData['sevenAge'] ?? '';
        eightAgeController.text = _formData['eightAge'] ?? '';
        nineAgeController.text = _formData['nineAge'] ?? '';
        // Continue for other controllers
      }
      notifyListeners();  // Notify widgets of state changes
    } catch (e) {
      // Handle errors
    }
  }

  Future<void> saveFormStep(int stepIndex, Map<String, dynamic> formData) async {
    _currentStep = stepIndex;
    _formData.addAll(formData); // Use `addAll` to merge new data with existing map
    try {
      await _preferences?.setInt('form_step', stepIndex);
      await _preferences?.setString('form_data', json.encode(_formData));
      notifyListeners();  // Notify widgets listening for state changes
    } catch (e) {
      // Handle errors, perhaps logging them
    }
  }

  Future<void> resetForm() async {
    await _preferences?.clear(); // Clear all saved data

    // Clear all text controllers
    dateController.clear();
    emailController.clear();
    ageController.clear();
    firstAgeController.clear();
    secondAgeController.clear();
    thirdAgeController.clear();
    fourthAgeController.clear();
    fifthAgeController.clear();
    sixAgeController.clear();
    sevenAgeController.clear();
    eightAgeController.clear();
    nineAgeController.clear();
    // Clear other controllers...

    // Clear any internal data maps
    _formData = {};

    notifyListeners(); // Notify listeners to rebuild UI
  }
}