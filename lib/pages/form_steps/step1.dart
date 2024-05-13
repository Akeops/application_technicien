import 'package:flutter/material.dart';
import 'package:intl/intl.dart';  // Import the intl library for date and time formatting.

class StepDateOfBirth extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController dateController;
  final TextEditingController timeController; // Controller for the time
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepDateOfBirth({super.key, 
    required this.formKey,
    required TextEditingController dateController,
    required this.onNext,
    required this.onPrevious,
  }) : dateController = dateController..text = DateFormat('dd-MM-yyyy').format(DateTime.now()),
       timeController = TextEditingController()..text = DateFormat('HH:mm').format(DateTime.now()) {
    // Initialize the text controllers with today's date and current time.
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Date et heure d'arrivée"),
        automaticallyImplyLeading: false, // Removes the default back button
      ),
      body: Form(
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
                TextFormField(
                  controller: timeController,
                  decoration: const InputDecoration(
                    labelText: "Heure d'intervention",
                    suffixIcon: Icon(Icons.access_time),
                  ),
                  readOnly: true,
                  onTap: () async {
                    final TimeOfDay? pickedTime = await showTimePicker(
                      context: context,
                      initialTime: TimeOfDay.now(),
                    );
                    if (pickedTime != null) {
                      timeController.text = pickedTime.format(context);
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
                    onPressed: () {
                      if (formKey.currentState!.validate()) {
                        onNext(); // Only proceed if the form is valid
                      }
                    },
                    child: const Text('Suivant'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}