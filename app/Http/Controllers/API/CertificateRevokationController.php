<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class CertificateRevokationController extends Controller
{
   public function revokeCertificate(Request $request){
      $cert_srl = $request->input('certificate_srl');
      $device_cert = $this->loadCRL($cert_srl);

      if ($device_cert == NULL) {
         return response()->json([
         'message' => 'Revokation Failed!'
         ], 422);
      }

      $device_cert['is_revoked'] = true;
      $device_cert['revoked_at'] = DateTime.now();
      $device_cert['revokation_detail'] = DateTime.now();


      $result = Certificate::find($device_cert['id']);
      $result->is_revoked = $device_cert['is_revoked'];
      $result->update();

      return response()->json([
         'message' => 'Revokation Success!'
      ], 200);
   }

   public function checkLicenceValidation(Request $request)
   {
      $cert_srl = $request->input('certificate_srl');
      $device_cert = $this->loadCRL($cert_srl);

      // Check certificate is revoke or not expired
      if (($device_cert['is_revoked']) || $device_cert['valid_end'] > DateTime . now()) {
         return false;
      }

      return true;
   }

   private function loadCRL($cert_srl)
   {
      $user_id = Auth::user()->id;
      $certificates = User::find($user_id)->certificate->all();

      if (is_array($certificates)) {
         foreach ($certificates as $cert) {
            if ($cert['certificate_srl'] == $cert_srl) {
               $device_cert = $cert;
            }
         }
      } else {
         $device_cert = $certificates;
      }

      return $device_cert;
   }
}
