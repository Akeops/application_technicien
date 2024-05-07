import 'package:application_tacteo/pages/form_state_storage.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class StepAge extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final int currentStep;
  final TextEditingController codeClientController;
  final TextEditingController designationController;
  final TextEditingController siretController;
  final TextEditingController mailController;
  final TextEditingController phoneNumberController;
  final TextEditingController addressController;
  final TextEditingController additionalAddressController;
  final TextEditingController cityController;
  final TextEditingController postalCodeController;
  final TextEditingController searchController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepAge({super.key, 
    required this.formKey,
    this.currentStep = 2,
    required this.codeClientController,
    required this.designationController,
    required this.siretController,
    required this.mailController,
    required this.phoneNumberController,
    required this.addressController,
    required this.additionalAddressController,
    required this.cityController,
    required this.postalCodeController,
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
              buildRow(codeClientController, designationController, 'Code client*', 'Désignation*', isFieldOneRequired: true, isFieldTwoRequired: true),
              buildTextField(siretController, 'Numéro SIRET*', true),
              buildRow(mailController, phoneNumberController, 'Mail*', 'Telephone', isFieldOneRequired: true),
              buildTextField(addressController, 'Adresse', true),
              buildTextField(additionalAddressController, 'Complément d\'adresse'),
              buildRow(cityController, postalCodeController, 'Ville', 'Code postal', isFieldTwoRequired: false),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (formKey.currentState!.validate()) {
                    Map<String, String> formData = {
                      'codeClient': codeClientController.text,
                      'designation': designationController.text,
                      'siret': siretController.text,
                      'mail': mailController.text,
                      'phoneNumber': phoneNumberController.text,
                      'address': addressController.text,
                      'additionalAddress': additionalAddressController.text,
                      'city': cityController.text,
                      'postalCode': postalCodeController.text,
                      // Add other fields similarly
                    };
                    // Assuming '2' is the index of this step
                    Provider.of<myFormState>(context, listen: false).saveFormStep(currentStep, formData);
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


