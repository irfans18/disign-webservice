<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kreait\Laravel\Firebase\Facades\FirebaseStorage;


class StorageController extends Controller
{
   public function upload(Request $request)
   {

      $file = $request->file('file');
      $path = $file->store('pdfs', 'public');

      return response()->json(['path' => $path]);

      // $file = $request->file('file');

      // if ($file) {
      //    $path = $file->store('pdfs', 'gcs'); // Upload the file to Firebase Storage

      //    return response()->json([
      //       'message' => 'PDF uploaded successfully',
      //       'path' => $path,
      //    ]);
      // }
      // return response()->json([
      //    'message' => 'PDF uploaded Failed!',
      //    // 'path' => $path,
      // ]);

      // $path = $request->file('file')->store('uploads', 'firebase');

      // // Save the file path to the 'requests' table using the provided schema

      // return response()->json(['path' => $path]);
   }

   public function getPdf($path)
   {
      $url = FirebaseStorage::storage()->url($path);

      return response()->json(['url' => $url]);
   }

   public function download($filePath)
   {
      return Storage::disk('firebase')->download($filePath);
   }

   public function delete($filePath)
   {
      Storage::disk('firebase')->delete($filePath);

      // Delete the file path from the 'requests' table using the provided schema

      return response()->json(['message' => 'File deleted successfully']);
   }
}
