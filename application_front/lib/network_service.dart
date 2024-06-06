// import 'dart:io';
// import 'dart:convert';
// import 'package:http/http.dart' as http;
// import 'package:http_parser/http_parser.dart';  // Import for MediaType

// class NetworkService {
//   Future<void> uploadFormData(File imageFile, List<int>? signatureBytes) async {
//     Uri url = Uri.parse('https://yourbackend.example.com/upload');
//     var request = http.MultipartRequest('POST', url);

//     // Add the image file to the multipart request
//     request.files.add(
//       await http.MultipartFile.fromPath(
//         'image', 
//         imageFile.path,
//         contentType: MediaType('image', 'jpeg'), // Adjust the media type based on your file type
//       )
//     );
  
//     // Add the signature image to the multipart request
//     if (signatureBytes != null) {
//       request.files.add(
//         http.MultipartFile.fromBytes(
//           'signature', 
//           signatureBytes,
//           contentType: MediaType('image', 'png'), // Assuming signature is in PNG format
//         )
//       );
//     }

//     // Add other form fields if necessary
//     // request.fields['key'] = 'value';

//     try {
//       var response = await request.send();
//       if (response.statusCode == 200) {
//         print("Upload successful");
//       } else {
//         print('Failed to upload. Status code: ${response.statusCode}');
//         print('Reason: ${await response.stream.bytesToString()}');
//       }
//     } catch (e) {
//       print('Exception caught: $e');
//     }
//   }
// }