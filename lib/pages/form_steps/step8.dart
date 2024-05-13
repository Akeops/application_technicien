import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SignatoryInformation extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const SignatoryInformation({
    super.key,
    required this.formKey,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _SignatoryInformationState createState() => _SignatoryInformationState();
}

class _SignatoryInformationState extends State<SignatoryInformation> {
  final TextEditingController _textInputController = TextEditingController();
  String? _selectedOption1;
  String? _selectedOption2;
  final List<String> _options1 = ['Option 1', 'Option 2', 'Option 3'];
  final List<String> _options2 = ['Choice A', 'Choice B', 'Choice C'];

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
      _textInputController.text = prefs.getString('textInput') ?? '';
    });
  }

  Future<void> _savePreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('selectedOption1', _selectedOption1 ?? _options1.first);
    await prefs.setString('selectedOption2', _selectedOption2 ?? _options2.first);
    await prefs.setString('textInput', _textInputController.text);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Custom Page"),
        automaticallyImplyLeading: false,
      ),
      body: Form(
        key: widget.formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              DropdownButtonFormField<String>(
                value: _selectedOption1,
                hint: const Text("Select Option"),
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
              const SizedBox(height: 20),
              TextFormField(
                controller: _textInputController,
                decoration: const InputDecoration(
                  labelText: 'Enter Details',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter some text';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 20),
              DropdownButtonFormField<String>(
                value: _selectedOption2,
                hint: const Text("Choose Preference"),
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
              const SizedBox(height: 40),
              ElevatedButton(
                onPressed: () {
                  if (widget.formKey.currentState!.validate()) {
                    _savePreferences();
                    widget.onNext();
                  }
                },
                child: const Text('Next'),
              ),
              ElevatedButton(
                onPressed: widget.onPrevious,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                ),
                child: const Text('Back'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}