import 'package:flutter/material.dart';

class StepEmail extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController emailController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepEmail(
      {required this.formKey,
      required this.emailController,
      required this.onNext,
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
                controller: emailController,
                decoration: InputDecoration(labelText: 'Entrer votre email'),
                validator: (value) => value == null || value.isEmpty
                    ? 'Ce champ ne peut pas être vide'
                    : null,
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: onNext,
                child: Text('Suivant'),
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
