<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kreait\Laravel\Firebase\Facades\FirebaseStorage;


class StorageController extends Controller
{
   public function upload(Request $request)
   {

      if ($request->hasFile('file')) {
         
         $file = $request->file('file');
         $path = $file->store('pdfs', 'public');
         $filename = basename($path);
         return response()->json(['path' => $path, 'name' => $filename], 201);
      }
      return response()->json(['error' => 'File not found.'], 400);

   }

   public function show($filename)
   {
      // $path = 'pdfs/' . $filename;
      $path = 'pdfs/' . $filename;
      if (Storage::disk('public')->exists($path)) {
         return Storage::disk('public')->response($path);
      }
      return response()->json(['error' => 'File not found.'], 404);
   }

   public function download($filePath)
   {
      return Storage::disk('public')->download($filePath);
   }

   public function delete($filePath)
   {
      Storage::disk('firebase')->delete($filePath);

      // Delete the file path from the 'requests' table using the provided schema

      return response()->json(['message' => 'File deleted successfully']);
   }
}
