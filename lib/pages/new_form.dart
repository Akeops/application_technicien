import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'form_steps/step1.dart';
import 'form_steps/step2.dart';
import 'form_steps/step3.dart';
import 'form_steps/step4.dart';
import 'form_steps/step5.dart';
import 'form_steps/step6.dart';
import 'form_steps/step7.dart';
import 'form_steps/step8.dart';

class MultiStepForm extends StatefulWidget {
  const MultiStepForm({super.key});

  @override
  MultiStepFormState createState() => MultiStepFormState();
}

class MultiStepFormState extends State<MultiStepForm> {
  final PageController _pageController = PageController();
  late final List<GlobalKey<FormState>> _formKeys = List.generate(7, (index) => GlobalKey<FormState>());
  
  final TextEditingController _dateController = TextEditingController();
  final TextEditingController _timeController = TextEditingController();
  final TextEditingController _interventionController = TextEditingController();
  final TextEditingController _codeClientController = TextEditingController();
  final TextEditingController _designationController = TextEditingController();
  final TextEditingController _siretController = TextEditingController();
  final TextEditingController _mailController = TextEditingController();
  final TextEditingController _phoneNumberController = TextEditingController(); 
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _additionalAddressController = TextEditingController();
  final TextEditingController _cityController = TextEditingController();
  final TextEditingController _postalCodeController = TextEditingController();
  final TextEditingController _interventionDecriptionController = TextEditingController();
  final TextEditingController _softwareInformationController = TextEditingController();
  final TextEditingController _billingController = TextEditingController();
  final TextEditingController _totalWithoutTaxesController = TextEditingController();
  final TextEditingController _vatController = TextEditingController();
  final TextEditingController _includingDiscountController = TextEditingController();
  final TextEditingController _totalPriceController = TextEditingController();
  String selectedOption1 = "Default value"; // Default value

  @override
  void initState() {
    super.initState();
    _loadSavedStep();
  }

  @override
  void dispose() {
    _disposeControllers();
    super.dispose();
  }

  void _disposeControllers() {
    _dateController.dispose();
    _timeController.dispose();
    _interventionController.dispose();
    _codeClientController.dispose();
    _designationController.dispose();
    _siretController.dispose(); 
    _mailController.dispose();
    _phoneNumberController.dispose();
    _addressController.dispose();
    _additionalAddressController.dispose();
    _cityController.dispose();
    _postalCodeController.dispose();
    _interventionDecriptionController.dispose();
    _softwareInformationController.dispose();
    _billingController.dispose();
    _pageController.dispose();
    _totalWithoutTaxesController.dispose();
    _vatController.dispose();
    _includingDiscountController.dispose();
    _totalPriceController.dispose();
  }

  void _loadSavedStep() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    int savedStep = prefs.getInt('currentStep') ?? 0;
    _loadDataFromPrefs(prefs);
    selectedOption1 = prefs.getString('selectedOption1') ?? "Default Option";
    if (savedStep != 0) {
      _pageController.jumpToPage(savedStep);
    }
  }

  void _loadDataFromPrefs(SharedPreferences prefs) {
    _dateController.text = prefs.getString('dateOfBirth') ?? '';
    _interventionController.text = prefs.getString('intervention') ?? '';
    _codeClientController.text = prefs.getString('codeClient') ?? '';
    _designationController.text = prefs.getString('designation') ?? '';
    _siretController.text = prefs.getString('siret') ?? '';
    _mailController.text = prefs.getString('mail') ?? '';
    _phoneNumberController.text = prefs.getString('phoneNumber') ?? '';
    _addressController.text = prefs.getString('address') ?? '';
    _additionalAddressController.text = prefs.getString('additionalAddress') ?? '';
    _cityController.text = prefs.getString('city') ?? '';
    _postalCodeController.text = prefs.getString('postalCode') ?? '';
    _interventionDecriptionController.text = prefs.getString('description') ?? '';
    _softwareInformationController.text = prefs.getString('softwareInformation') ?? '';
    _billingController.text = prefs.getString('billing') ?? '';
    _totalWithoutTaxesController.text = prefs.getString('totalWithoutTaxes') ?? '';
    _vatController.text = prefs.getString('vat') ?? '';
    _includingDiscountController.text = prefs.getString('includingDiscount') ?? '';
    _totalPriceController.text = prefs.getString('totalPrice') ?? '';
  }

  void _nextPage() {
    if (_pageController.hasClients && _pageController.page!.toInt() < _formKeys.length - 1) {
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
    await _saveFormData(prefs);
  }

  Future<void> _saveFormData(SharedPreferences prefs) async {
    await prefs.setString('dateOfBirth', _dateController.text);
    await prefs.setString('intervention', _interventionController.text);
    await prefs.setString('codeClient', _codeClientController.text);
    await prefs.setString('designation', _designationController.text);
    await prefs.setString('siret', _siretController.text);
    await prefs.setString('mail', _mailController.text);
    await prefs.setString('phoneNumber', _phoneNumberController.text);
    await prefs.setString('address', _addressController.text);
    await prefs.setString('additionalAddress', _additionalAddressController.text);
    await prefs.setString('city', _cityController.text);
    await prefs.setString('postalCode', _postalCodeController.text);
    await prefs.setString('description', _interventionDecriptionController.text);
    await prefs.setString('softwareInformation', _softwareInformationController.text);
    await prefs.setString('billing', _billingController.text);
    await prefs.setString('totalWithoutTaxes', _totalWithoutTaxesController.text);
    await prefs.setString('vat', _vatController.text);
    await prefs.setString('includingDiscount', _includingDiscountController.text);
    await prefs.setString('totalPrice', _totalPriceController.text);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Multi-Step Form"),
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
            currentStep: 2,
            codeClientController: _codeClientController, 
            designationController: _designationController,
            siretController: _siretController,   
            mailController: _mailController,
            phoneNumberController: _phoneNumberController,
            addressController: _addressController,
            additionalAddressController: _additionalAddressController,
            cityController: _cityController,
            postalCodeController: _postalCodeController,
            onNext: _nextPage,
            onPrevious: _previousPage),
          StepDescriptionIntervention(formKey: _formKeys[3], interventionDecriptionController: _interventionDecriptionController, onNext: _nextPage, onPrevious: _previousPage),
          StepSoftwareInformation(formKey: _formKeys[4], softwareInformationController: _softwareInformationController, onNext: _nextPage, onPrevious: _previousPage),
          StepBilling(formKey: _formKeys[5], billingController: _billingController, onNext: _nextPage, onPrevious: _previousPage),
          ConfirmationPage(formKey: _formKeys[6],
          totalWithoutTaxesController: _totalWithoutTaxesController,
          vatController: _vatController,
          includingDiscountController: _includingDiscountController,
          totalPriceController: _totalPriceController, onNext: _nextPage, onPrevious: _previousPage, selectedOption1: selectedOption1,),
        ],
      ),
    );
  }
}