import 'package:flutter/material.dart';
//import 'package:google_fonts/google_fonts.dart';
const d_green = Color(0xFFFFFFFF);

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
        primaryColor: d_green, // Utilise la couleur personnalisée pour les éléments primaires
      ),
      home: HomePage(),
      );
  }
}

class HomePage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(title: Text('Home'),
        ),
        body: Container(
          color: Colors.amber,
        )
    );
  }
}
