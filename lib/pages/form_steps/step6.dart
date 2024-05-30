import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StepBilling extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController billingController;
  final void Function(String selectedOption1, String selectedOption2) onNext;
  final VoidCallback onPrevious;
  final String initialSelectedOption1;
  final String initialSelectedOption2;

  const StepBilling({
    super.key,
    required this.formKey,
    required this.billingController,
    required this.onNext,
    required this.onPrevious,
    required this.initialSelectedOption1,
    required this.initialSelectedOption2,
  });

  @override
  _StepBillingState createState() => _StepBillingState();
}

class _StepBillingState extends State<StepBilling> {
  late String _selectedOption1;
  late String _selectedOption2;
  final List<String> _options1 = ['BAB', 'Hors BAB -50km', 'Hors BAB +50km', '+100km'];
  final List<String> _options2 = ['Oui', 'Non'];

  @override
  @override
void initState() {
  super.initState();
  _selectedOption1 = _options1.contains(widget.initialSelectedOption1)
      ? widget.initialSelectedOption1
      : _options1.first;
  _selectedOption2 = _options2.contains(widget.initialSelectedOption2)
      ? widget.initialSelectedOption2
      : _options2.first;
}

  Future<void> _savePreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('selectedOption1', _selectedOption1);
    await prefs.setString('selectedOption2', _selectedOption2);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Facturation"),
        automaticallyImplyLeading: false,
      ),
      body: GestureDetector(
        onTap: () {
          FocusScope.of(context).unfocus();
        },
        child: Form(
          key: widget.formKey,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              Center(
                child: Container(
                  width: MediaQuery.of(context).size.width * 0.7,
                  child: DropdownButtonFormField<String>(
                    value: _options1.contains(_selectedOption1) ? _selectedOption1 : null,
                    hint: const Text("Déplacement"),
                    onChanged: (String? newValue) {
                      setState(() {
                        _selectedOption1 = newValue!;
                        _savePreferences();
                      });
                    },
                    items: _options1.map<DropdownMenuItem<String>>((String value) {
                      return DropdownMenuItem<String>(
                        value: value,
                        child: Text(value),
                      );
                    }).toList(),
                    validator: (value) => value == null ? 'Ce champ ne peut pas être vide' : null,
                  ),
                ),
              ),
              const SizedBox(height: 20),
              Center(
                child: Container(
                  width: MediaQuery.of(context).size.width * 0.7,
                  child: DropdownButtonFormField<String>(
                    value: _options2.contains(_selectedOption2) ? _selectedOption2 : null,
                    hint: const Text("Sous contrat"),
                    onChanged: (String? newValue) {
                      setState(() {
                        _selectedOption2 = newValue!;
                        _savePreferences();
                      });
                    },
                    items: _options2.map<DropdownMenuItem<String>>((String value) {
                      return DropdownMenuItem<String>(
                        value: value,
                        child: Text(value),
                      );
                    }).toList(),
                    validator: (value) => value == null ? 'Ce champ ne peut pas être vide' : null,
                  ),
                ),
              ),
              const SizedBox(height: 100),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: <Widget>[
                  SizedBox(
                    width: 150,
                    child: ElevatedButton(
                      onPressed: widget.onPrevious,
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
                        if (widget.formKey.currentState!.validate()) {
                          widget.onNext(_selectedOption1, _selectedOption2);
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
    );
  }
}
