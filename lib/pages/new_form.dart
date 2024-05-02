import 'package:flutter/material.dart';

class StepName extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController nameController;
  final VoidCallback onNext;

  StepName({required this.formKey, required this.nameController, required this.onNext});

  @override
  Widget build(BuildContext context) {
    return Form(
      key: formKey,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextFormField(
                controller: nameController,
                decoration: InputDecoration(labelText: 'Entrer votre nom'),
                validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: onNext,
                child: Text('Suivant'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class StepEmail extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController emailController;
  final VoidCallback onNext;
  final VoidCallback onPrevious;

  StepEmail({required this.formKey, required this.emailController, required this.onNext, required this.onPrevious});

  @override
  Widget build(BuildContext context) {
    return Form(
      key: formKey,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextFormField(
                controller: emailController,
                decoration: InputDecoration(labelText: 'Entrer votre email'),
                validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: onNext,
                child: Text('Suivant'),
              ),
              ElevatedButton(
                onPressed: onPrevious,
                child: Text('Précédent'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class StepAge extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController ageController;
  final VoidCallback onPrevious;

  StepAge({required this.formKey, required this.ageController, required this.onPrevious});

  @override
  Widget build(BuildContext context) {
    return Form(
      key: formKey,
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              TextFormField(
                controller: ageController,
                decoration: InputDecoration(labelText: 'Entrer votre âge'),
                validator: (value) => value == null || value.isEmpty ? 'Ce champ ne peut pas être vide' : null,
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (formKey.currentState!.validate()) {
                    print("Formulaire Complété! Nom: ${ageController.text}");
                    // Ici, vous pourriez envoyer les données à un serveur ou effectuer d'autres actions
                  }
                },
                child: Text('Terminer'),
              ),
              ElevatedButton(
                onPressed: onPrevious,
                child: Text('Précédent'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class MultiStepForm extends StatefulWidget {
  @override
  _MultiStepFormState createState() => _MultiStepFormState();
}

class _MultiStepFormState extends State<MultiStepForm> {
  final _pageController = PageController();
  final _formKeys = [GlobalKey<FormState>(), GlobalKey<FormState>(), GlobalKey<FormState>()];
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _ageController = TextEditingController();

  @override
  void dispose() {
    _nameController.dispose();
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
          StepName(formKey: _formKeys[0], nameController: _nameController, onNext: _nextPage),
          StepEmail(formKey: _formKeys[1], emailController: _emailController, onNext: _nextPage, onPrevious: _previousPage),
          StepAge(formKey: _formKeys[2], ageController: _ageController, onPrevious: _previousPage),
        ],
      ),
    );
  }
}