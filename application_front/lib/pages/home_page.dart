import 'package:application_tacteo/pages/form_state_storage.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

class HomePage extends StatelessWidget {
  final Color backgroundColor;

  HomePage({this.backgroundColor = Colors.amber});

  @override
  Widget build(BuildContext context) {
    // Lock the device orientation to portrait mode
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.portraitDown,
    ]);

    // Common button style
    final buttonStyle = ElevatedButton.styleFrom(
      foregroundColor: Colors.black,
      backgroundColor: Colors.white,
      minimumSize: const Size(150, 45), // Minimum width and height for buttons
      padding: const EdgeInsets.symmetric(horizontal: 16), // Horizontal padding for button content
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
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
                  image: AssetImage('assets/Tacteo_CB_converted.webp'),
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
                            Navigator.pushNamed(context, '/form');
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
                            Navigator.pushNamed(context, '/form'); 
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
      },
    );
  }
}