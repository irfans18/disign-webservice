<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use App\Models\Device;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\Request as ModelsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CertificateRevokationController extends Controller
{
   public function requestRevocation(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'serial' => 'required|string',
         'hwid' => 'required|string',
         'revocation_detail' => 'required|string',
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
            'file_upload' => null,
         ], 422);
      }

      return $this->createRequest($request->serial, $request->revocation_detail, $request->file);
   }
   public function createRequest($serial, $detail, $file)
   {
      $cert = $this->loadCRL($serial);

      if ($cert == NULL) {
         return response()->json([
            'revocation_status' => 'unknown',
            'message' => 'Revokation request failed. Certificate not found!',
            'file_upload' => null,
         ], 422);
      }

      if ($cert->is_revoked) {
         return response()->json([
            'revocation_status' => ModelsRequest::APRROVE,
            'message' => 'Certificate already revoked',
            'file_upload' => null,

         ], 422);
      }

      $filepath = $this->upload($file);

      $req = ModelsRequest::create([
         'certificate_id' => $cert->id,
         'status' => ModelsRequest::PENDING,
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
         'file_upload' => $filepath,

      ], 201);
   }

   public function getAllRequests()
   {
      return ModelsRequest::all();
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

   public function revoke($serial, $detail)
   {
      // $cert_srl = $request->input('certificate_srl');
      $cert = $this->loadCRL($serial);

      if ($cert == NULL) {
         return response()->json([
            'isRevoked' => false,
            'message' => 'Revokation Failed. Certificate not found!',
            'certificate' => null,
         ], 422);
      }

      if ($cert->is_revoked) {
         return response()->json([
            'isRevoked' => false,
            'message' => 'Certificate already revoked',
            'certificate' => $cert,

         ], 422);
      }

      $cert->is_revoked = true;
      $cert->revoked_at = time();
      $cert->revoked_timestamp = time();
      $cert->revokation_detail = $detail;
      $cert->update();

      return response()->json([
         'isRevoked' => true,
         'message' => 'Revokation Success!',
         'certificate' => $cert,
      ], 200);
   }

   public function checkLicenceValidation(Request $request)
   {
      $device = Device::where('hwid', $request->hwid)->first();
      $cert = Certificate::where('device_id', $device->id)->first();
      $cert_srl = $cert->certificate_srl;
      $device_cert = $this->loadCRL($cert_srl);
      // dd($device_cert);
      // Check certificate is revoke or not expired
      if ($device_cert->is_revoked || $device_cert['valid_end'] > time()) {
         return response()->json([
            'isValid' => false,
            'message' => 'Certificate is not valid.'
         ]);
      }

      return response()->json([
         'isValid' => true,
         'message' => 'Certificate still valid.'
      ]);
   }

   private function loadCRL($cert_srl)
   {
      $cert = Certificate::where('certificate_srl', $cert_srl)->first();
      return $cert;
   }
   
}
