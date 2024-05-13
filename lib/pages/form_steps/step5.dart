import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StepSoftwareInformation extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController softwareInformationController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const StepSoftwareInformation({
    super.key, 
    required this.formKey,
    required this.softwareInformationController,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _StepSoftwareInformationState createState() => _StepSoftwareInformationState();
}

class _StepSoftwareInformationState extends State<StepSoftwareInformation> {
  final Map<String, bool> _choices = {
    "Logiciel normé NF525": false,
    "Mise à jour réalisée": false,
    "Formation à la sauvegarde": false,
    "Test de bon fonctionnement": false,
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

  bool _validateSelection() {
    return _choices.values.any((value) => value);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Informations logicielles"),
        automaticallyImplyLeading: false,  // This prevents a back button from appearing
      ),
      body: GestureDetector(
        onTap: () {
          FocusScope.of(context).unfocus();  // Hide keyboard when tapping outside of text fields
        },
        child: Form(
          key: widget.formKey,
          child: Center(
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: <Widget>[
                    TextFormField(
                      controller: widget.softwareInformationController,
                      decoration: const InputDecoration(
                        labelText: 'Enter software information',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
                    ),
                    const SizedBox(height: 20),
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
                      onPressed: () {
                        if (_validateSelection()) {
                          widget.onNext();
                        } else {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Please select at least one option before proceeding.'))
                          );
                        }
                      },
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
          ),
        ),
      ),
    );
  }
}