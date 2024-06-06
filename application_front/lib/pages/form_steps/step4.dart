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
    return Scaffold(
      appBar: AppBar(
        title: const Text("Description de l'intervention"),  // Set the title for your current page context
        automaticallyImplyLeading: false,  // Removes the default back button
      ),
      body: GestureDetector(
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
                const SizedBox(height: 60),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,  // Distributes space evenly around the children
                  children: <Widget>[
                    SizedBox(
                      width: 150,  // Specify the width of the button for 'Précédent'
                      child: ElevatedButton(
                        onPressed: onPrevious,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.grey,  // Consistent with the 'Previous' button
                        ),
                        child: const Text('Précédent'),
                      ),
                    ),
                    SizedBox(
                      width: 150,  // Ensure this is the same as the first button to maintain uniformity for 'Suivant'
                      child: ElevatedButton(
                        onPressed: () {
                          if (formKey.currentState!.validate()) {
                            onNext();
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

  Future<void> _saveDescription(String description) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('interventionDescription', description);
  }
}