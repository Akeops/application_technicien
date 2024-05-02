import 'package:flutter/material.dart';
//import 'package:google_fonts/google_fonts.dart';
// ignore: constant_identifier_names
const d_white = Color(0xFFFFFFFF);

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Tacteo application',
      theme: ThemeData(
        primarySwatch: Colors.green, // Utilise un ensemble préconçu de nuances de vert
        primaryColor: d_white, // Utilise la couleur personnalisée pour les éléments primaires
        elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          foregroundColor: Colors.white, backgroundColor: Colors.black,  // Définit la couleur du texte pour tous les ElevatedButtons
        ),
      ),
      ),
      
      home: HomePage(),
      );
  }
}

class HomePage extends StatelessWidget {
  final Color backgroundColor;

  HomePage({this.backgroundColor = Colors.amber});

  @override
  Widget build(BuildContext context) {
    // Style commun pour les boutons
    final buttonStyle = ElevatedButton.styleFrom(
      foregroundColor: Colors.black, backgroundColor: Colors.white,
      minimumSize: const Size(120, 36), // Définit une largeur minimale et une hauteur pour les boutons
      padding: const EdgeInsets.symmetric(horizontal: 16), // Padding horizontal pour le contenu du bouton
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(10),
        side: const BorderSide(color: Colors.white, width: 2.0),
      ),
    );

    return Scaffold(
      appBar: AppBar(title: const Text('APPLICATION TECHNICIEN')),
      body: Center(
        child: Container(
          decoration: const BoxDecoration(
            color: Colors.black,
            image: DecorationImage(
              image: AssetImage('assets/Tacteo_CB.png'),
              fit: BoxFit.fitWidth, // Pour ajuster l'image à la taille du Container
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
                        // Action when the first button is pressed
                      }, 
                      style: buttonStyle,
                      child: const Text('Reprendre'),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: ElevatedButton(
                      onPressed: () {
                        // Action when the second button is pressed
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
}


