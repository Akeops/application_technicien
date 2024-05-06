import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StepIntervention extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  late final TextEditingController interventionController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepIntervention({
    required this.formKey,
    required this.interventionController,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _StepInterventionState createState() => _StepInterventionState();
}

class _StepInterventionState extends State<StepIntervention> {
  final Map<String, bool> _choices = {
    "Option 1": false,
    "Option 2": false,
    "Option 3": false,
    "Option 4": false,
    "Option 5": false,
    "Option 6": false,
    "Option 7": false,
  };

  @override
  void initState() {
    super.initState();
    _loadChoices();
  }

  Future<void> _loadChoices() async {
    final prefs = await SharedPreferences.getInstance();
    _choices.forEach((key, value) {
      bool savedValue = prefs.getBool(key) ?? false;
      setState(() {
        _choices[key] = savedValue;
      });
    });
  }

  void _toggleChoice(String key) {
    setState(() {
      _choices[key] = !_choices[key]!;
    });
    _saveChoices();
  }

  Future<void> _saveChoices() async {
    final prefs = await SharedPreferences.getInstance();
    _choices.forEach((key, value) {
      prefs.setBool(key, value);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: widget.formKey,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              ..._choices.keys.map((String key) {
                return CheckboxListTile(
                  title: Text(key),
                  value: _choices[key],
                  onChanged: (bool? value) {
                    _toggleChoice(key);
                  },
                );
              }).toList(),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: widget.onNext,
                child: const Text('Suivant'),
              ),
              ElevatedButton(
                onPressed: widget.onPrevious,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                ),
                child: const Text('Précédent'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}