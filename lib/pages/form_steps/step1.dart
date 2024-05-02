import 'package:flutter/material.dart';

class StepDateOfBirth extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController dateController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const StepDateOfBirth(
      {required this.formKey,
      required this.dateController,
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
                controller: dateController,
                decoration: const InputDecoration(
                  labelText: 'Date de naissance',
                  suffixIcon: Icon(Icons.calendar_today),
                ),
                readOnly: true,
                onTap: () async {
                  DateTime? pickedDate = await showDatePicker(
                    context: context,
                    initialDate: DateTime.now(),
                    firstDate: DateTime(1900),
                    lastDate: DateTime.now(),
                  );
                  if (pickedDate != null) {
                    dateController.text =
                        "${pickedDate.toLocal()}".split(' ')[0];
                  }
                },
                validator: (value) => value == null || value.isEmpty
                    ? 'Ce champ ne peut pas être vide'
                    : null,
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: onNext,
                child: const Text('Suivant'),
              ),
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
}
