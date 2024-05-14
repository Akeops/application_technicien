import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SignatoryInformation extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController civilityController;
  final TextEditingController nameController;
  final TextEditingController qualityController;
  final TextEditingController signatoryInformationController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  const SignatoryInformation({
    super.key,
    required this.formKey,
    required this.civilityController,
    required this.nameController,
    required this.qualityController,
    required this.signatoryInformationController,
    required this.onNext,
    required this.onPrevious,
  });

  @override
  _SignatoryInformationState createState() => _SignatoryInformationState();
}

class _SignatoryInformationState extends State<SignatoryInformation> {
  String? _selectedCivility;
  String? _selectedQuality;
  final List<String> _civilityOptions = ['Monsieur', 'Madame'];
  final List<String> _qualityOptions = ['Gérant(e)', 'Responsable', 'Directeur(ice)', 'Salarié(e)', 'Président(e)'];

  @override
  void initState() {
    super.initState();
    _loadPreferences();
  }

  Future<void> _loadPreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      _selectedCivility = prefs.getString('selectedCivility') ?? _civilityOptions.first;
      _selectedQuality = prefs.getString('selectedQuality') ?? _qualityOptions.first;
    });
  }

  Future<void> _savePreferences() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('selectedCivility', _selectedCivility ?? _civilityOptions.first);
    await prefs.setString('selectedQuality', _selectedQuality ?? _qualityOptions.first);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Information signataire"),
        automaticallyImplyLeading: false,
      ),
      body: GestureDetector(
        onTap: () => FocusScope.of(context).unfocus(),
        child: Form(
          key: widget.formKey,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                buildDropdown(_selectedCivility, _civilityOptions, 'Select Civility', (newValue) {
                  setState(() => _selectedCivility = newValue);
                  _savePreferences();
                }),
                const SizedBox(height: 20),
                TextFormField(
                  controller: widget.signatoryInformationController,
                  decoration: const InputDecoration(
                    labelText: 'Nom*',
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter signatory details';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 20),
                buildDropdown(_selectedQuality, _qualityOptions, 'Select Quality', (newValue) {
                  setState(() => _selectedQuality = newValue);
                  _savePreferences();
                }),
                const SizedBox(height: 80), // Increased spacing for visual separation
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
        ),
      ),
    );
  }

  Widget buildDropdown(String? currentValue, List<String> options, String hint, void Function(String?) onChanged) {
    return DropdownButtonFormField<String>(
      value: currentValue,
      hint: Text(hint),
      onChanged: onChanged,
      items: options.map<DropdownMenuItem<String>>((String value) {
        return DropdownMenuItem<String>(
          value: value,
          child: Text(value),
        );
      }).toList(),
    );
  }
}