import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class myFormState with ChangeNotifier {
  SharedPreferences? _preferences;
  int _currentStep = 0;

  int get currentStep => _currentStep;

  myFormState() {
    _loadFromPrefs();
  }

  Future<void> _loadFromPrefs() async {
    _preferences = await SharedPreferences.getInstance();
    _currentStep = _preferences?.getInt('form_step') ?? 0;
    notifyListeners();  // Notifie les widgets à l'écoute d'un changement d'état
  }

  Future<void> saveFormStep(int stepIndex) async {
    _currentStep = stepIndex;
    await _preferences?.setInt('form_step', stepIndex);
    notifyListeners();  // Notifie les widgets à l'écoute d'un changement d'état
  }
}