<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use App\Models\Device;
use App\Models\Certificate;
use Illuminate\Http\Request;
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
         'revokation_detail' => 'required|string|min:6',
         // 'revoked_timestamp' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }
      $user_id = Auth::user()->id;

      // $device = Device::select('*')->where('hwid', $request->hwid)->first()->toArray();
      $device = Device::where('hwid', $request->hwid)->first();
      dd($user_id, $device);
      if ($user_id != $device->user_id) {
         return response()->json([
            'isRevoked' => false,
            'message' => 'Revokation Failed. Device not found!',
            'certificate' => $cert,
         ], 422);
      }
      // $cert = Certificate::where('device_id', $device->id)->first();

      return $this->revoke($request->serial, $request->revokation_detail);
   }

   public function revoke($serial, $detail)
   {
      // $cert_srl = $request->input('certificate_srl');
      $cert = $this->loadCRL($serial);

      if ($cert == NULL) {
         return response()->json([
            'isRevoked' => false,
            'message' => 'Revokation Failed. Certificate not found!',
            'certificate' => $cert,
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
      ], 202);
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
      // $user_id = Auth::user()->id;
      // $certificates = User::find($user_id)->certificates;
      // dd($certificates);

      // if (is_array($certificates)) {
      //    foreach ($certificates as $cert) {
      //       if ($cert['certificate_srl'] == $cert_srl) {
      //          $device_cert = $cert;
      //       }
      //    }
      // } else {
      //    $device_cert = $certificates;
      // }
      $cert = Certificate::where('certificate_srl', $cert_srl)->first();

      return $cert;
   }
   private function loadPem()
   {
      // echo(storage_path("app/public/subca1.crt"));
      // echo(storage_path("app/public/subca1.key"));
      // $pkey = File::get(storage_path("app/public/subca1.key"));
      $pkey = <<<EOD
-----BEGIN ENCRYPTED PRIVATE KEY-----
MIIFHzBJBgkqhkiG9w0BBQ0wPDAbBgkqhkiG9w0BBQwwDgQIGwMgpxO/moYCAggA
MB0GCWCGSAFlAwQBKgQQE01/JjXpgXMHSndSdzLzogSCBNDerW7UZ4cFI8d09GAd
u5OQ3bJOyxuyreUgTE8Zg3w+2egmvyXYyd7dgq9ZTWIWa5aq3rVi5omTEVWDbLWk
GFLBASF8r9RLBzGWW8t1kDd8KULplzCwdfve6tAR1zIVsQNO/FiPeDrMBKRDcRBh
nYehBM8fm+xS/Qnwdvn974mNsVYwQc8AwFVo0GvgJyb+9LJBQOfkuETkOMJYs407
Pq07cSEwJpq3/17I9PUmrGJeoR3CDPY61HyqB/mbLI4sgY6B0YM28s6HyVcHyFoi
5+rNEvMcYlmMceLZGDDN1DQZG2JqFzS7e39ZRGITAuFkk760262IQ6+vUUnXYTG6
qit5WP8OlarONVWITEOnX1Lp8/iHtvVGmRrxdfG99m4WKQgJ3SciJBFYHGm44p4z
XZ8u1nDDY+wqugwOmdW5FOkNmcwXxaaxtJQ1fCO6AuW4QUduvJzmRISZJlpAtoK8
3ei+l9boMYmx4i+aaNOsO7XRw2UvzCjOUYZNY6mm4XyXbHnJmswZVWB0KPW0FvYS
bFSy1lTWjb1atq+2zMNWn+cHlZ/ZU9BmEPOauhufVxzj0K4yWtqJn1E0JJKtVooh
PjEerMD3KEQz+uGQ1/L1dSCfMEHyLcDm/gWxeaMPl5pAsnntT/ptCV9CjFNdEHzG
1Xb0NH4CVDRAeL55tJKvOwF/ZaS9QV4aStCieHlQgLqgCzLtbtKm2yZb4vc7DS+A
2mGGdwycd+I76Rq+GqEY1JhUDzuKML65UNcjx0h0L6ibPjIrwQw1TZy6Rk17f6tt
P4adV0FWL0/8dNudJIGqrNyZnKSbfS713pFPvKjiCfN2LXNfMNiMMQRG4ofiLCbZ
D6KICqXdhCeRYSiBQqlIzWcpOVe/8tycbpmxFmwGUWX6mKYauFOknUjfIqXqc4+H
0Jv7y5KTohistcs4NgirOy6GYdfrmQejzn36NP+DV8OeHS1X+OGyNg6SapCMxiKU
Ryhd4lvY0m7ZLgDhz+NTHz7VX4DXFc1sRWTVkMBsucqU2ROnSi+DcVD7CEvsAMrb
lXGttsmFnE5vHP4KwiR2+XQqy6CKI0mlQ9VNutSbk/Qqgg6YdY9ocZWA1abfw8tD
sWrYvtJBBVlLxQPtp5clLyt9o7/Mwu5wHsOTO66s+1rWYTeM05zy/PDsfNTTSQkb
8panVE8hSdQ3J0x8hFd/diYFIdQWpz+M5RlUHLlXTxj9y9AeEbYhAZS0zuI2ryLe
muEyRQ9AGMoNbr0dIav0yEm9Wr22LQ+aYAA+7HMn3qWgxQwv2gbrVrseeCbtz2Lr
ljNgEkY9YBlScZGsgzz0NOcuJ/gPTAgW+GDzeWbOpa/vR/3nNkImR0bNyc9ZMS08
9aJOOzCztlTF0KrFT7crdsizx4UKqr2VZPVYsXGq9whomv/DfokPCRyB+qHiPr+3
gQ03lDrv7fst5qY/l6Z0YzE52guRBGkhfA/odNtvw6dnVfjW2U3EfzKXbLTSRvFD
T48U+ilQ2CVGuBT1mjE/VqL6JM9RE7ZUbagr8dZfFuhjBz8YFijl4b5rE7WOXNtY
63qFPrnLISghNne5igGJCV9gYFkmT+fWw7revIVmcOl/QNy3ZD2rg2+jcOjr0ROT
nqUc3QCnc6uaUjYsLf57ejzWRA==
-----END ENCRYPTED PRIVATE KEY-----
EOD;
      // $cert = File::get(storage_path("app/public/subca1.crt"));
      $cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIIEmjCCAoICCQDmWiDnU9MSoTANBgkqhkiG9w0BAQUFADCBiDELMAkGA1UEBhMC
SUQxFjAUBgNVBAgMDURJIFlvZ3lha2FydGExDzANBgNVBAcMBlNsZW1hbjEVMBMG
A1UECgwMZGlzaWduLCBJbmMuMQ0wCwYDVQQLDARDb3JlMQ0wCwYDVQQDDAREU0lE
MRswGQYJKoZIhvcNAQkBFgxpdEBkaXNpZ24uaWQwHhcNMjMwNTA3MTMxMTA0WhcN
MjQwNTA2MTMxMTA0WjCBlDELMAkGA1UEBhMCSUQxFDASBgNVBAgMC0RLSSBKYWth
cnRhMRYwFAYDVQQHDA1KYWthcnRhIFB1c2F0MRUwEwYDVQQKDAxkaXNpZ24sIElu
Yy4xDzANBgNVBAsMBlNlcnZlcjEOMAwGA1UEAwwFU0RTSUQxHzAdBgkqhkiG9w0B
CQEWEHNlcnZlckBkaXNpZ24uaWQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEK
AoIBAQC8n3E0ncliIY2qNSnPEr1C+3dFio5f8GA04/6GpRDSNa66CeRM03cPRWc7
Z11EhgydLonXzgOt3N10kKMvt6QLCxJ7Wxe2tb/OlXFVll7IewPY1iGViL0mQNwp
5vsHEqQ0fGlTMLff8mmsN473PPdsF1NKdn3v24ZcrvN62kRd3EyA6aPQshlNCU84
ozNAnTyy53Kg8cNSLPsbhBBLIS6SKpqXihVqqhTVMAK+wmSYXF6+BFjz0cyT8At+
v50evAsmJ9OahlzNzQc7R/XNW697qiGocEKH0k+MG6Gbs3WOtjoaV7lJngGW6SvX
JfBA/Hu+OYSKm/WB+uRq6h73Lph7AgMBAAEwDQYJKoZIhvcNAQEFBQADggIBAIQz
pQyTFTH5jnPo3OI13ThhQKl0adOTQ6KBYfzzPDaDT3aML8k8eHrEVJ4Pmdab4Lfb
CcnHBgKnKkR7JOWR9kdF2mRzuEAqF83YdEbTqR0PgK/UIzZBB+NsQgYtVGYl+bl0
ViK2hq1YUPqzsnJhMISH5GR0yaGUWRC5uJh+c/uI6MIR7stfSv2tjJQ7qubctsVB
JFFGRBFfb/qGvCAKUfojILAFpSa9ADAYGxWf176UDafphSo2UAbTpQSevy3FapAN
ifZ6xiiqeyv8kzhRqR/zlj4CKuOkgK18UCNVWTABWe91exKQ96VpR51KD+idJpTF
EjSwbloy0D8pO5zZD9LzVb8H8lCuHZZ1oq4WOZASRmxIvp5N/U9d7CGJf8QHGAv1
b0jNIVKf2EUNs6Y176pNJ/eKGUCD+uyxZx+Z5dM69YfpI73yAn2Vmoa3ipScdslT
z9cwgiI1sKS3fl1psKgP5FyCoym/U12I4j9D7Cy+DG/ICo2AE7q+sMD+G2fIzrW+
5D0A2mXmnSp3UXJoT78P9dA81qsGL+bdvmIYXVBFn0d7VnLCwDDJFEA94LVa69l9
LQx+smsu+zvMayJKabAj0v8Icw2pnjNIvJdL+ZXDWLDRNVmIoGZppWiUC/8Jx2Yn
Wm69PDh23C+Dr3b81AyZO7rjbjcSbf4Jsgd8DrbL
-----END CERTIFICATE-----
EOD;
      // echo $pkey, $cert;
      // dd($pkey,$cert);
      return response()->json([
         "cert" => $cert,
         "pkey" => $pkey,
      ]);
   }

   // // Revoke a certificate
   // function revoke($certificateSerialNumber = 1, $existingCrl = null)
   // {
   //    $pem = ($this->loadPem()->getData());
   //    $ca_pkey_pem = $pem->pkey;
   //    $ca_cert_pem = $pem->cert;

   //    $ci = array(
   //       'no' => 1,
   //       'version' => 2,
   //       'days' => 30,
   //       'alg' => OPENSSL_ALGO_SHA1,
   //       'revoked' => array(
   //          array(
   //             'serial' => $certificateSerialNumber,
   //             'rev_date' => time(),
   //             'reason' => X509::getRevokeReasonCodeByName("privilegeWithdrawn"),
   //             'compr_date' => strtotime("-1 day"),
   //             'hold_instr' => null,
   //          )
   //       )
   //    );
   //    // $ca_pkey = openssl_get_privatekey($ca_pkey_pem, '1234');
   //    $ca_cert = openssl_x509_read($ca_cert_pem);
   //    $ca_pkey = openssl_pkey_get_private($ca_pkey_pem, '1234');
   //    // $ca_cert = X509::pem2der(file_get_contents('ca_cert.cer'));
   //    $ca_cert = X509::pem2der($ca_cert_pem);
   //    $crl_data = X509_CRL::create($ci, $ca_pkey, $ca_cert);
   //    $crl_pem = X509::der2pem4crl($crl_data);
   //    return $crl_pem;
   // }


   // You can save the newCrl string to a file if needed:
   // file_put_contents('new_crl.pem', $newCrl);
}
