import 'package:flutter/material.dart';

class ConfirmationPage extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController totalWithoutTaxesController;
  final TextEditingController vatController;
  final TextEditingController includingDiscountController;
  final TextEditingController totalPriceController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const ConfirmationPage({
    Key? key,
    required this.formKey,
    required this.totalWithoutTaxesController,
    required this.vatController, 
    required this.includingDiscountController,
    required this.totalPriceController, 
    required this.onNext,
    required this.onPrevious, 
  }) : super(key: key);

  @override
  _ConfirmationPageState createState() => _ConfirmationPageState();
}

class _ConfirmationPageState extends State<ConfirmationPage> {
  String? selectedOption;
  double price = 0;

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
                  SizedBox(height: 20),
                  Text("Selected Option: ${selectedOption ?? "None"}", style: TextStyle(fontSize: 16)),
                  SizedBox(height: 10),
                  Text("Price to Pay: \$${price.toStringAsFixed(2)}", style: TextStyle(fontSize: 20)),
                  SizedBox(height: 30),
                  buildTextField("First Name", widget.totalWithoutTaxesController),
                  buildTextField("Last Name", widget.vatController),
                  buildTextField("Email Address", widget.includingDiscountController),
                  buildTextField("totalPrice", widget.totalPriceController),
                  SizedBox(height: 20), 
                  ElevatedButton(
                    onPressed: widget.onNext,
                    child: Text('Next'),
                  ),
                  ElevatedButton(
                    onPressed: widget.onPrevious,
                    child: Text('Back'),
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