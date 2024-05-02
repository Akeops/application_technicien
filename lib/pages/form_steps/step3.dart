import 'package:flutter/material.dart';

class StepAge extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController ageController;
  final VoidCallback onPrevious;

  StepAge(
      {required this.formKey,
      required this.ageController,
      required this.onPrevious});

  @override
  Widget build(BuildContext context) {
    return Form(
      key: formKey,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextFormField(
                controller: ageController,
                decoration: InputDecoration(labelText: 'Entrer votre âge'),
                validator: (value) => value == null || value.isEmpty
                    ? 'Ce champ ne peut pas être vide'
                    : null,
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (formKey.currentState!.validate()) {
                    print("Formulaire Complété! Nom: ${ageController.text}");
                    // Ici, vous pourriez envoyer les données à un serveur ou effectuer d'autres actions
                  }
                },
                child: Text('Terminer'),
              ),
              ElevatedButton(
                onPressed: onPrevious,
                child: Text('Précédent'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
