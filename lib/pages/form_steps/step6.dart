import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StepBilling extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController billingController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const StepBilling({
    super.key,
    required this.formKey,
    required this.billingController,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _StepBillingState createState() => _StepBillingState();
}

class _StepBillingState extends State<StepBilling> {
  String? _selectedOption1;
  String? _selectedOption2;
  final List<String> _options1 = ['BAB', 'Hors BAB -50km', 'Hors BAB +50km', '+100km'];
  final List<String> _options2 = ['Oui', 'Non'];

  @override
  void initState() {
    super.initState();
    _loadPreferences();
  }

  Future<void> _loadPreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      _selectedOption1 = prefs.getString('selectedOption1') ?? _options1.first;
      _selectedOption2 = prefs.getString('selectedOption2') ?? _options2.first;
    });
  }

  Future<void> _savePreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('selectedOption1', _selectedOption1 ?? _options1.first);
    await prefs.setString('selectedOption2', _selectedOption2 ?? _options2.first);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Facturation"),
        automaticallyImplyLeading: false,  // Disables the back button
      ),
      body: Form(
        key: widget.formKey,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: <Widget>[
            Center(
              child: Container(
                width: MediaQuery.of(context).size.width * 0.7, // Reduced width and centered
                child: DropdownButtonFormField<String>(
                  value: _selectedOption1,
                  hint: const Text("Déplacement"),
                  onChanged: (String? newValue) {
                    setState(() {
                      _selectedOption1 = newValue;
                      _savePreferences();
                    });
                  },
                  items: _options1.map<DropdownMenuItem<String>>((String value) {
                    return DropdownMenuItem<String>(
                      value: value,
                      child: Text(value),
                    );
                  }).toList(),
                ),
              ),
            ),
            const SizedBox(height: 20),
            Center(
              child: Container(
                width: MediaQuery.of(context).size.width * 0.7, // Same as the first dropdown
                child: DropdownButtonFormField<String>(
                  value: _selectedOption2,
                  hint: const Text("Sous contrat"),
                  onChanged: (String? newValue) {
                    setState(() {
                      _selectedOption2 = newValue;
                      _savePreferences();
                    });
                  },
                  items: _options2.map<DropdownMenuItem<String>>((String value) {
                    return DropdownMenuItem<String>(
                      value: value,
                      child: Text(value),
                    );
                  }).toList(),
                ),
              ),
            ),
            const SizedBox(height: 100),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: <Widget>[
                SizedBox(
                  width: 150,  // Specify the width of the button for 'Précédent'
                  child: ElevatedButton(
                    onPressed: widget.onPrevious,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.grey,
                    ),
                    child: const Text('Précédent'),
                  ),
                ),
                SizedBox(
                  width: 150,  // Ensure this is the same as the first button to maintain uniformity for 'Suivant'
                  child: ElevatedButton(
                    onPressed: () {
                      if (widget.formKey.currentState!.validate()) {
                        widget.onNext();
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
    );
  }
}