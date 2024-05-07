import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StepDescriptionIntervention extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController interventionDecriptionController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepDescriptionIntervention({
    super.key,
    required this.formKey,
    required this.interventionDecriptionController,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
        onTap: () {
            // Call this method here to hide keyboard
            FocusScope.of(context).requestFocus(FocusNode());
        },
        child: Form(
            key: formKey,
            child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: <Widget>[
                        TextFormField(
                            controller: interventionDecriptionController,
                            decoration: const InputDecoration(
                                labelText: 'Description de l’intervention',
                                hintText: 'Décrivez ce que l’intervention implique',
                                border: OutlineInputBorder(),
                            ),
                            maxLines: 5,
                            validator: (value) {
                                if (value == null || value.isEmpty) {
                                    return 'Veuillez entrer une description';
                                }
                                return null;
                            },
                        ),
                        const SizedBox(height: 40),
                        ElevatedButton(
                            onPressed: () async {
                                if (formKey.currentState!.validate()) {
                                    await _saveDescription(interventionDecriptionController.text);
                                    onNext();
                                }
                            },
                            child: const Text('Suivant'),
                        ),
                        const SizedBox(height: 10), // Spacing between buttons
                        ElevatedButton(
                            onPressed: onPrevious,
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

  Future<void> _saveDescription(String description) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('interventionDescription', description);
  }
}