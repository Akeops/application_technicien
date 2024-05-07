import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'form_steps/step1.dart';
import 'form_steps/step2.dart';
import 'form_steps/step3.dart';
import 'form_steps/step4.dart';

class MultiStepForm extends StatefulWidget {
  const MultiStepForm({super.key});

  @override
  MultiStepFormState createState() => MultiStepFormState();
}

class MultiStepFormState extends State<MultiStepForm> {
  final _pageController = PageController();
  late final List<GlobalKey<FormState>> _formKeys;
  final int numberOfSteps = 4;  // Total number of form steps
  final _dateController = TextEditingController();
  final _timeController = TextEditingController();
  final _interventionController = TextEditingController();
  final _firstAgeController = TextEditingController();
  final _secondAgeController = TextEditingController();
  final _thirdAgeController = TextEditingController();
  final _fourthAgeController = TextEditingController();
  final _fifthAgeController = TextEditingController(); 
  final _sixAgeController = TextEditingController();
  final _sevenAgeController = TextEditingController();
  final _eightAgeController = TextEditingController();
  final _nineAgeController = TextEditingController();
  final _interventionDecriptionController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _formKeys = List<GlobalKey<FormState>>.generate(numberOfSteps, (index) => GlobalKey<FormState>());
    _loadSavedStep();
  }

  @override
  void dispose() {
    _dateController.dispose();
    _timeController.dispose();
    _interventionController.dispose();
    _firstAgeController.dispose();
    _secondAgeController.dispose();
    _thirdAgeController.dispose(); 
    _fourthAgeController.dispose();
    _fifthAgeController.dispose();
    _sixAgeController.dispose();
    _sevenAgeController.dispose();
    _eightAgeController.dispose();
    _nineAgeController.dispose();
    _interventionDecriptionController.dispose();
    super.dispose();
  }

  void _loadSavedStep() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    int savedStep = prefs.getInt('currentStep') ?? 0;
    _dateController.text = prefs.getString('dateOfBirth') ?? '';
    _interventionController.text = prefs.getString('intervention') ?? '';
    _firstAgeController.text = prefs.getString('firstAge') ?? '';
    _secondAgeController.text = prefs.getString('secondAge') ?? '';
    _thirdAgeController.text = prefs.getString('secondAge') ?? '';
    _fourthAgeController.text = prefs.getString('secondAge') ?? '';
    _fifthAgeController.text = prefs.getString('secondAge') ?? '';
    _sixAgeController.text = prefs.getString('secondAge') ?? '';
    _sevenAgeController.text = prefs.getString('secondAge') ?? '';
    _eightAgeController.text = prefs.getString('secondAge') ?? '';
    _nineAgeController.text = prefs.getString('secondAge') ?? '';

    if (savedStep != 0) {
      _pageController.jumpToPage(savedStep);
    }
  }

  void _nextPage() {
    if (_pageController.hasClients && _pageController.page!.toInt() < (_formKeys.length - 1)) {
      _pageController.nextPage(duration: const Duration(milliseconds: 300), curve: Curves.easeIn).then((_) {
        _saveCurrentStep();
      });
    }
  }

  void _previousPage() {
    if (_pageController.hasClients && _pageController.page!.toInt() > 0) {
      _pageController.previousPage(duration: const Duration(milliseconds: 300), curve: Curves.easeIn).then((_) {
        _saveCurrentStep();
      });
    }
  }

  void _saveCurrentStep() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    int currentPage = _pageController.page!.toInt();
    await prefs.setInt('currentStep', currentPage);
    await prefs.setString('dateOfBirth', _dateController.text);
    await prefs.setString('intervention', _interventionController.text);
    await prefs.setString('firstAge', _firstAgeController.text);
    await prefs.setString('secondAge', _secondAgeController.text);
    await prefs.setString('thirdAge', _thirdAgeController.text);
    await prefs.setString('fourthAge', _fourthAgeController.text);
    await prefs.setString('fifthAge', _fifthAgeController.text);
    await prefs.setString('sixAge', _sixAgeController.text);
    await prefs.setString('sevenAge', _sevenAgeController.text);
    await prefs.setString('eightAge', _eightAgeController.text);
    await prefs.setString('nineAge', _nineAgeController.text);
    await prefs.setString('Description', _interventionDecriptionController.text);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Formulaire Multi-Étapes"),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () {
            _saveCurrentStep();
            Navigator.of(context).pop();
          },
        ),
      ),
      body: PageView(
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: <Widget>[
          StepDateOfBirth(formKey: _formKeys[0], dateController: _dateController, onNext: _nextPage, onPrevious: () {  }),
          StepIntervention(formKey: _formKeys[1], interventionController: _interventionController, onNext: _nextPage, onPrevious: _previousPage),
          StepAge(
            formKey: _formKeys[2],
            firstAgeController: _firstAgeController, 
            secondAgeController: _secondAgeController,
            thirdAgeController: _thirdAgeController,   
            fourthAgeController: _fourthAgeController,
            fifthAgeController: _fifthAgeController,
            sixAgeController: _sixAgeController,
            sevenAgeController: _sevenAgeController,
            eightAgeController: _eightAgeController,
            nineAgeController: _nineAgeController,
            onNext: _nextPage,
            onPrevious: _previousPage),
          StepDescriptionIntervention(formKey: _formKeys[3], interventionDecriptionController: _interventionDecriptionController, onNext: _nextPage, onPrevious: _previousPage)
        ],
      ),
    );
  }
}