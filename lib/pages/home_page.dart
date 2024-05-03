import 'package:application_tacteo/pages/new_form.dart';
import 'package:application_tacteo/pages/form_state_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
// ignore: constant_identifier_names

class HomePage extends StatelessWidget {
  final Color backgroundColor;
  

  HomePage({this.backgroundColor = Colors.amber});

  @override
  Widget build(BuildContext context) {
    // Bloquer l'orientation de l'appareil en mode portrait
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.portraitDown,
    ]);
    // Style commun pour les boutons
    final buttonStyle = ElevatedButton.styleFrom(
      foregroundColor: Colors.black, backgroundColor: Colors.white,
      minimumSize: const Size(150, 60), // Définit une largeur minimale et une hauteur pour les boutons
      padding: const EdgeInsets.symmetric(horizontal: 16), // Padding horizontal pour le contenu du bouton
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(10),
        side: const BorderSide(color: Colors.white, width: 2.0),
      ),
    );

    return LayoutBuilder(
      builder: (context, constraints) {
        return Scaffold(
          appBar: AppBar(title: const Text('APPLICATION TECHNICIEN')),
          body: Center(
            child: Container(
              decoration: const BoxDecoration(
                color: Colors.black,
                image: DecorationImage(
                  image: AssetImage('assets/Tacteo_CB.png'),
                  fit: BoxFit.fitWidth, 
                  alignment: Alignment(0.0, -0.6),
                ),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: <Widget>[
                  const SizedBox(height: 20), // Adds spacing between the text and the first button
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: <Widget>[ 
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: ElevatedButton(
                          onPressed: () {
                              Navigator.of(context).push(
                                MaterialPageRoute(builder: (context) => MultiStepForm())
                              );
                          }, 
                          style: buttonStyle,
                          child: const Text('Reprendre'),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: ElevatedButton(
                          onPressed: () {
                            Provider.of<myFormState>(context, listen: false).resetForm();
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (context) => MultiStepForm(key: UniqueKey())),
                            );
                          }, 
                          style: buttonStyle,
                          child: const Text('Nouveau'),
                        ),
                      ),
                    ],
                  )
                ],
              ),
            ),
          ),
        );
      }
    );
  }
}




