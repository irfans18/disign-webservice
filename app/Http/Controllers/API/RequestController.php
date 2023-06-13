<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
   public function requestRevocation(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'serial' => 'required|string',
         'hwid' => 'required|string',
         'revokation_detail' => 'required|string',
         // 'revoked_timestamp' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }
      $user = Auth::user();

      $device = $user->devices->where('hwid', $request->hwid)->first();
      // $device = Device::where('hwid', $request->hwid)->where()->first();
      // dd($device);
      if ($device == null) {
         return response()->json([
            'revocation_status' => 'unknown',
            'message' => 'Revokation request failed. Device not found!',
         ], 422);
      }

      return $this->createRequest($request->serial, $request->revokation_detail);
   }
   public function createRequest($serial, $detail)
   {
      $cert = $this->loadCRL($serial);

      if ($cert == NULL) {
         return response()->json([
            'revocation_status' => 'unknown',
            'message' => 'Revokation request failed. Certificate not found!',
         ], 422);
      }

      if ($cert->is_revoked) {
         return response()->json([
            'revocation_status' => ModelsRequest::APRROVE,
            'message' => 'Certificate already revoked',

         ], 422);
      }

      $req = ModelsRequest::create([
         'certificate_id' => $cert->id,
         'filepath' => $filepath,
         'revocation_detail' => $detail,
         'revoked_at' => time(),
         'revoked_timestamp' => time(),
      ]);

      // $cert->is_revoked = true;
      // $cert->revoked_at = time();
      // $cert->revoked_timestamp = time();
      // $cert->revokation_detail = $detail;
      // $cert->update();

      return response()->json([
         'revocation_status' => ModelsRequest::PENDING,
         'message' => 'Revocation request created!',
      ], 201);
   }
   private function loadCRL($cert_srl)
   {
      return Certificate::where('certificate_srl', $cert_srl)->first();

      // return $cert;
   }
   public function upload($file)
   {
      if ($file) {
         // $file = $request->file('file');
         $path = $file->store('pdfs', 'public');
         $filename = basename($path);
         return $filename;
      }
      return 'File not uploaded!';
   }
}
