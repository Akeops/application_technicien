import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'pages/home_page.dart';
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
          textStyle: const TextStyle(fontSize: 20),
          foregroundColor: Colors.white, backgroundColor: Colors.black,  // Définit la couleur du texte pour tous les ElevatedButtons
        ),
      ),
      ),
      
      home: HomePage(),
      );
  }
}




