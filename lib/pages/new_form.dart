import 'package:flutter/material.dart';
import 'form_steps/step1.dart';
import 'form_steps/step2.dart';
import 'form_steps/step3.dart';


class MultiStepForm extends StatefulWidget {
  @override
  _MultiStepFormState createState() => _MultiStepFormState();
}

class _MultiStepFormState extends State<MultiStepForm> {
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

  void _nextPage() {
    _pageController.nextPage(duration: Duration(milliseconds: 300), curve: Curves.easeIn);
  }

  void _previousPage() {
    _pageController.previousPage(duration: Duration(milliseconds: 300), curve: Curves.easeInOut);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Formulaire Multi-Étapes"),
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