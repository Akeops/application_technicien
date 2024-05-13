import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ConfirmationPage extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController totalWithoutTaxesController;
  final TextEditingController vatController;
  final TextEditingController includingDiscountController;
  final TextEditingController totalPriceController;
  String? selectedOption1;  // Allow it to be null
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  ConfirmationPage({
    Key? key,
    required this.formKey,
    required this.totalWithoutTaxesController,
    required this.vatController,
    required this.includingDiscountController,
    required this.totalPriceController,
    this.selectedOption1,
    required this.onNext,
    required this.onPrevious,
  }) : super(key: key);

  @override
  _ConfirmationPageState createState() => _ConfirmationPageState();
}

class _ConfirmationPageState extends State<ConfirmationPage> {
  double price = 0; // Assuming price calculation is handled elsewhere

  @override
  void initState() {
    super.initState();
    _loadSelectedOption();
  }

  Future<void> _loadSelectedOption() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    String? loadedOption = prefs.getString('selectedOption1');
    if (loadedOption != null && loadedOption != widget.selectedOption1) {
      setState(() {
        widget.selectedOption1 = loadedOption;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: GestureDetector(
        behavior: HitTestBehavior.opaque,
        onTap: () {
          FocusScope.of(context).unfocus();
        },
        child: Center(
          child: SingleChildScrollView(
            child: Form(
              key: widget.formKey,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: <Widget>[
                  const SizedBox(height: 20),
                  Text("Selected Option: ${widget.selectedOption1 ?? "Not Selected"}", style: const TextStyle(fontSize: 16)),
                  const SizedBox(height: 10),
                  Text("Price to Pay: \$${price.toStringAsFixed(2)}", style: const TextStyle(fontSize: 20)),
                  const SizedBox(height: 30),
                  buildTextField("Total HT", widget.totalWithoutTaxesController),
                  buildTextField("Dont remise", widget.vatController),
                  buildTextField("TVA 20%", widget.includingDiscountController),
                  buildTextField("Total TTC", widget.totalPriceController),
                  const SizedBox(height: 20),
                  ElevatedButton(
                    onPressed: widget.onNext,
                    child: const Text('Suivant'),
                  ),
                  ElevatedButton(
                    onPressed: widget.onPrevious,
                    child: const Text('Précédent'),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget buildTextField(String label, TextEditingController controller) {
    return Container(
      width: MediaQuery.of(context).size.width * 0.6,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Text(label, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          SizedBox(height: 8),
          TextField(
            controller: controller,
            decoration: InputDecoration(
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8.0),
              ),
              focusedBorder: OutlineInputBorder(
                borderSide: BorderSide(color: Theme.of(context).primaryColor, width: 2.0),
                borderRadius: BorderRadius.circular(8.0),
              ),
            ),
          ),
          SizedBox(height: 16),
        ],
      ),
    );
  }
}