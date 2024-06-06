import 'package:flutter/material.dart';

class ConfirmationPage extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController totalWithoutTaxesController;
  final TextEditingController vatController;
  final TextEditingController includingDiscountController;
  final TextEditingController totalPriceController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;
  final String selectedOption1;
  final String selectedOption2;

  ConfirmationPage({
    required this.formKey,
    required this.totalWithoutTaxesController,
    required this.vatController,
    required this.includingDiscountController,
    required this.totalPriceController,
    required this.onNext,
    required this.onPrevious,
    required this.selectedOption1,
    required this.selectedOption2,
  });

  @override
  Widget build(BuildContext context) {
    double formFieldWidth = MediaQuery.of(context).size.width * 0.8;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Confirmation"),
        automaticallyImplyLeading: false,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Form(
            key: formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                Text("Option 1 sélectionnée: $selectedOption1"),
                const SizedBox(height: 10),
                Text("Option 2 sélectionnée: $selectedOption2"),
                const SizedBox(height: 10),
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: Container(
                    width: formFieldWidth,
                    child: TextFormField(
                      controller: totalWithoutTaxesController,
                      decoration: const InputDecoration(
                        labelText: 'Total HT',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: Container(
                    width: formFieldWidth,
                    child: TextFormField(
                      controller: vatController,
                      decoration: const InputDecoration(
                        labelText: 'TVA',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: Container(
                    width: formFieldWidth,
                    child: TextFormField(
                      controller: includingDiscountController,
                      decoration: const InputDecoration(
                        labelText: 'Remise incluse',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: Container(
                    width: formFieldWidth,
                    child: TextFormField(
                      controller: totalPriceController,
                      decoration: const InputDecoration(
                        labelText: 'Total TTC',
                        border: OutlineInputBorder(),
                      ),
                      validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
                    ),
                  ),
                ),
                const SizedBox(height: 40),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: <Widget>[
                    SizedBox(
                      width: 150,
                      child: ElevatedButton(
                        onPressed: onPrevious,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.grey,
                        ),
                        child: const Text('Précédent'),
                      ),
                    ),
                    SizedBox(
                      width: 150,
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
}
