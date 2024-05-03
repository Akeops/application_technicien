import 'package:flutter/material.dart';
import 'package:intl/intl.dart';  // Import the intl library for date formatting.

class StepDateOfBirth extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController dateController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepDateOfBirth({
    required this.formKey,
    required TextEditingController dateController,
    required this.onNext,
    required this.onPrevious,
  }) : dateController = dateController..text = DateFormat('dd-MM-yyyy').format(DateTime.now()) {
    // Initialize the text controller with today's date in 'yyyy-MM-dd' format.
  }

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
                  labelText: "Date d'intervention",
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
                    dateController.text = DateFormat('dd-MM-yyyy').format(pickedDate);
                  }
                },
                validator: (value) {
                  return value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null;
                },
              ),
              const SizedBox(height: 20),
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: ElevatedButton(
                  onPressed: onNext,
                  child: const Text('Suivant'),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}