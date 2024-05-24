import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'pages/home_page.dart';
import 'pages/form_state_storage.dart';
import 'pages/new_form.dart';
import 'pages/auth/login_page.dart';
import 'pages/auth/register_page.dart';
//import io.flutter.embedding.android.FlutterActivity;

// Define your color constant
const d_white = Color(0xFFFFFFFF);

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('token');
  
  runApp(
    ChangeNotifierProvider(

      create: (context) => myFormState(),
      child: MyApp(initialRoute: token == null ? '/home' : '/home'),
    ),
  );
}

class MyApp extends StatelessWidget {
  final String initialRoute;

  MyApp({required this.initialRoute});

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
      initialRoute: initialRoute,
      routes: {
        '/login': (context) => LoginPage(),
        '/register': (context) => RegisterPage(),
        '/home': (context) => HomePage(),
        '/form': (context) => MultiStepForm(),
        // Add other routes here if needed
      },
    );
  }
}