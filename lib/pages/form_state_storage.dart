import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';

class myFormState with ChangeNotifier {
  SharedPreferences? _preferences;
  int _currentStep = 0;
  Map<String, dynamic> _formData = {};

  int get currentStep => _currentStep;
  TextEditingController dateController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController ageController = TextEditingController();
  Map<String, dynamic> get formData => _formData;

  myFormState() {
    _loadFromPrefs();
    dateController.text = DateFormat('dd-MM-yyyy').format(DateTime.now());
  }

  Future<void> _loadFromPrefs() async {
    try {
      _preferences = await SharedPreferences.getInstance();
      _currentStep = _preferences?.getInt('form_step') ?? 0;
      String? formDataJson = _preferences?.getString('form_data');
      if (formDataJson != null) {
        _formData = json.decode(formDataJson);
      }
      notifyListeners();  // Notifie les widgets à l'écoute d'un changement d'état
    } catch (e) {
      // Gestion des erreurs
      _currentStep = 0;  // Valeur par défaut en cas d'erreur
      _formData = {};    // Réinitialisation des données du formulaire
      notifyListeners();
    }
  }

  Future<void> saveFormStep(int stepIndex, Map<String, dynamic> formData) async {
    _currentStep = stepIndex;
    _formData = formData;
    try {
      await _preferences?.setInt('form_step', stepIndex);
      await _preferences?.setString('form_data', json.encode(formData));
      notifyListeners();  // Notifie les widgets à l'écoute d'un changement d'état
    } catch (e) {
      // Gestion des erreurs
    }
  }

  Future<void> resetForm() async {
    await _preferences?.clear(); // Clear all saved data

    // Clear all text controllers
    //dateController.clear();
    emailController.clear();
    ageController.clear();

    // Clear any internal data maps
    _formData = {};

    notifyListeners(); // Notify listeners to rebuild UI
  }
}