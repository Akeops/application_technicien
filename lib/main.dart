import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'pages/home_page.dart';
import 'pages/form_state_storage.dart';
import 'pages/new_form.dart'; // Import your form page

// Define your color constant
const d_white = Color(0xFFFFFFFF);

void main() {
  runApp(
    ChangeNotifierProvider(
      create: (context) => myFormState(),
      child: MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Tacteo Application',
      theme: ThemeData(
        primarySwatch: Colors.green,
        primaryColor: d_white,
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            textStyle: const TextStyle(fontSize: 20),
            foregroundColor: Colors.white,
            backgroundColor: Colors.black,
          ),
        ),
      ),
      initialRoute: '/',
      routes: {
        '/': (context) => HomePage(),
        '/form': (context) => MultiStepForm(),
        // Add other routes here if needed
      },
    );
  }
}