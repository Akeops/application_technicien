import 'package:application_tacteo/pages/form_state_storage.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class StepAge extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController firstAgeController;
  final TextEditingController secondAgeController;
  final TextEditingController thirdAgeController;
  final TextEditingController fourthAgeController;
  final TextEditingController fifthAgeController;
  final TextEditingController sixAgeController;
  final TextEditingController sevenAgeController;
  final TextEditingController eightAgeController;
  final TextEditingController nineAgeController;
  final TextEditingController searchController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepAge({super.key, 
    required this.formKey,
    required this.firstAgeController,
    required this.secondAgeController, 
    required this.thirdAgeController,
    required this.fourthAgeController,
    required this.fifthAgeController,
    required this.sixAgeController,
    required this.sevenAgeController,
    required this.eightAgeController,
    required this.nineAgeController,
    required this.onNext,
    required this.onPrevious,
  }) : searchController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Form(
        key: formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              buildSearchBar(context),
              buildRow(firstAgeController, secondAgeController, 'Code client*', 'Désignation*', isFieldOneRequired: true, isFieldTwoRequired: true),
              buildTextField(thirdAgeController, 'Numéro SIRET*', true),
              buildRow(fourthAgeController, fifthAgeController, 'Mail*', 'Telephone', isFieldOneRequired: true),
              buildTextField(sixAgeController, 'Adresse', true),
              buildTextField(sevenAgeController, 'Complément d\'adresse'),
              buildRow(eightAgeController, nineAgeController, 'Ville', 'Code postal', isFieldTwoRequired: true),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (formKey.currentState!.validate()) {
                    Map<String, String> formData = {
                      'firstAge': firstAgeController.text,
                      'secondAge': secondAgeController.text,
                      'thirdAge': thirdAgeController.text,
                      'fourthAge': thirdAgeController.text,
                      'fifthAge': thirdAgeController.text,
                      'sixAge': thirdAgeController.text,
                      'sevenAge': thirdAgeController.text,
                      'eightAge': thirdAgeController.text,
                      'nineAge': thirdAgeController.text,
                      // Add other fields similarly
                    };
                    // Assuming '2' is the index of this step
                    Provider.of<myFormState>(context, listen: false).saveFormStep(2, formData);
                    onNext();
                  }
                },
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

  Widget buildRow(TextEditingController controllerOne, TextEditingController controllerTwo, String labelOne, String labelTwo, {bool isFieldOneRequired = false, bool isFieldTwoRequired = false}) {
    return Row(
      children: <Widget>[
        Expanded(
          child: TextFormField(
            controller: controllerOne,
            decoration: InputDecoration(labelText: labelOne),
            validator: isFieldOneRequired ? (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null : null,
          ),
        ),
        const SizedBox(width: 10), // Space between fields
        Expanded(
          child: TextFormField(
            controller: controllerTwo,
            decoration: InputDecoration(labelText: labelTwo),
            validator: isFieldTwoRequired ? (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null : null,
          ),
        ),
      ],
    );
  }

  Widget buildTextField(TextEditingController controller, String labelText, [bool isRequired = false]) {
    return TextFormField(
      controller: controller,
      decoration: InputDecoration(labelText: labelText),
      validator: isRequired ? (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null : null,
    );
  }

  Widget buildSearchBar(BuildContext context) {
    final TextEditingController searchController = TextEditingController(); // Initialisé immédiatement dans la méthode
    return TextField(
      controller: searchController,
      decoration: InputDecoration(
        labelText: 'Rechercher',
        suffixIcon: IconButton(
          icon: const Icon(Icons.search),
          onPressed: () {
            executeSearch(searchController.text);
            searchController.clear();
            FocusScope.of(context).requestFocus(FocusNode());
          },
        ),
        border: const OutlineInputBorder(),
      ),
      onSubmitted: (value) {
        executeSearch(value); 
        searchController.clear();
        FocusScope.of(context).unfocus();
      },
    );
  }

  void executeSearch(String query) {
    print("Searching for: $query");
  }


