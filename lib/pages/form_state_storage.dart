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
  //TextEditingController ageController = TextEditingController(); // Third page
  TextEditingController codeClientController = TextEditingController();
  TextEditingController designationController = TextEditingController();
  TextEditingController siretController = TextEditingController();
  TextEditingController mailController = TextEditingController();
  TextEditingController phoneNumberController = TextEditingController();
  TextEditingController addressController = TextEditingController();
  TextEditingController additionalAddressController = TextEditingController();
  TextEditingController cityController = TextEditingController();
  TextEditingController postalCodeController = TextEditingController();
  TextEditingController softwareInformationController = TextEditingController();
  TextEditingController billingController = TextEditingController();
  TextEditingController firstNameController = TextEditingController();
  TextEditingController lastNameController = TextEditingController();
  TextEditingController dontRemiseController = TextEditingController();
  TextEditingController totalTTCController = TextEditingController();
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
        codeClientController.text = _formData['codeClient'] ?? '';
        designationController.text = _formData['designation'] ?? '';
        siretController.text = _formData['siret'] ?? '';
        mailController.text = _formData['mail'] ?? '';
        phoneNumberController.text = _formData['phoneNumber'] ?? '';
        addressController.text = _formData['address'] ?? '';
        additionalAddressController.text = _formData['additionalAddress'] ?? '';
        cityController.text = _formData['city'] ?? '';
        postalCodeController.text = _formData['postalCode'] ?? '';
        softwareInformationController.text = _formData['softwareInformation'] ?? '';
        billingController.text = _formData['billing'] ?? '';
        firstNameController.text = _formData['firstName'] ?? '';
        lastNameController.text = _formData['lastName'] ?? '';
        dontRemiseController.text = _formData['dontRemise'] ?? '';
        totalTTCController.text = _formData['totalTTC'] ?? '';
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
    String encodedData = json.encode(_formData);
    print("Encoded JSON data: $encodedData");  // Debug the JSON string
    await _preferences?.setString('form_data', encodedData);
    notifyListeners();  // Notify widgets listening for state changes
  } catch (e) {
    print("Error saving form data: $e");  // Log any errors
  }
  }

  Future<void> resetForm() async {
    await _preferences?.clear(); // Clear all saved data

    // Clear all text controllers
    dateController.clear();
    emailController.clear();
    //ageController.clear();
    codeClientController.clear();
    designationController.clear();
    siretController.clear();
    mailController.clear();
    phoneNumberController.clear();
    addressController.clear();
    additionalAddressController.clear();
    cityController.clear();
    postalCodeController.clear();
    softwareInformationController.clear();
    billingController.clear();
    // Clear other controllers...
 
    _formData = {};

    notifyListeners(); // Notify listeners to rebuild UI
  }
}