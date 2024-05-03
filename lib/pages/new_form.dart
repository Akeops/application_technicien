import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'form_steps/step1.dart';
import 'form_steps/step2.dart';
import 'form_steps/step3.dart';



class MultiStepForm extends StatefulWidget {
  const MultiStepForm({super.key});

  @override
  MultiStepFormState createState() => MultiStepFormState();
}

class MultiStepFormState extends State<MultiStepForm> {
  final _pageController = PageController();
  final _formKeys = [GlobalKey<FormState>(), GlobalKey<FormState>(), GlobalKey<FormState>()];
  final _dateController = TextEditingController();
  final _emailController = TextEditingController();
  final _ageController = TextEditingController();

  @override
  void dispose() {
    _dateController.dispose();
    _emailController.dispose();
    _ageController.dispose();
    super.dispose();
  }

  @override
void initState() {
  super.initState();
  _loadSavedStep();
}

void _loadSavedStep() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  int savedStep = prefs.getInt('currentStep') ?? 0;
  print("Loaded saved step: $savedStep"); // Log pour le débogage
  if (savedStep != 0) {
    _pageController.jumpToPage(savedStep);
  }
}

  void _nextPage() {
    _pageController.nextPage(duration: const Duration(milliseconds: 300), curve: Curves.easeIn).then((_) {
        _saveCurrentStep();
    });
  }

  void _previousPage() {
    _pageController.previousPage(duration: const Duration(milliseconds: 300), curve: Curves.easeIn).then((_) {
        _saveCurrentStep();
    });
  }

  void _saveCurrentStep() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  int currentPage = _pageController.page!.toInt();
  await prefs.setInt('currentStep', currentPage);
  print("Current step saved: $currentPage"); // Ajoutez ce log pour le débogage
}

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
      title: Text("Formulaire Multi-Étapes"),
      leading: IconButton(
        icon: Icon(Icons.arrow_back),
        onPressed: () {
          _saveCurrentStep();  // Assurez-vous que ceci sauvegarde l'état correctement
          Navigator.of(context).pop();
        },
      ),
    ),
      body: PageView(
        controller: _pageController,
        physics: NeverScrollableScrollPhysics(),
        scrollDirection: Axis.vertical,
        children: <Widget>[
          StepDateOfBirth(formKey: _formKeys[0], dateController: _dateController, onNext: _nextPage, onPrevious: () {  },),
          StepEmail(formKey: _formKeys[1], emailController: _emailController, onNext: _nextPage, onPrevious: _previousPage),
          StepAge(formKey: _formKeys[2], ageController: _ageController, onPrevious: _previousPage),
        ],
      ),
    );
  }
}