<?php

namespace App\Http\Controllers\API;

use App\Models\Device;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class CertificateSigningRequestController extends Controller
{
   public function signCsr(Request $request)
   {
      $pem = ($this->loadPem()->getData());
      $ca_pkey_pem = $pem->pkey;
      $ca_cert_pem = $pem->cert;
      // dd($pem);

      $ca_pkey = openssl_get_privatekey($ca_pkey_pem, '1234');
      $ca_cert = openssl_x509_read($ca_cert_pem);
      // dd([$ca_pkey, $ca_cert]);

      $opt = array(
         'digest_alg' => 'sha256',
         'x509_extensions' => 'v3_req'
      );
      
      // dd(json_decode($request->getContent(), true));
      // dd($request->input());
      $csr = $request->input('csr');
      $pubKey = $request->input('pubkey');
      // $content = $request->getContent();
      // dd($result);
      // if ($result != null) {
      //    $csr = $result;
      // } 
//       else {
//          $csr = "-----BEGIN CERTIFICATE REQUEST-----
// MIICmTCCAYECAQAwVDEMMAoGA1UEAwwDLi4uMQwwCgYDVQQGEwMuLi4xDDAKBgNV
// BAgMAy4uLjEMMAoGA1UEBwwDLi4uMQwwCgYDVQQKDAMuLi4xDDAKBgNVBAsMAy4u
// LjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMjwoglSS5D9Qoze1p95
// b6waDjKMdUvr5fsO7Tb9Glj/kYF8lgL6V4dpP/bWEc1kCaIKaUHYWIEeygmpQhRH
// 0EGioU9aHoXxRaVOKZ8dBIVseihqNZPG8uyIuEL/Z3tOaf1skwU1jNv3GQM3sxAz
// ytl34IdzkRQmQp4lBvBc51xLV37F57QbF0+sNYyYLaSQyXt6J0GU9YvoCFP6itly
// WeLFsmXlutwy2FSOzJk1wLeERLEytJ1Ml1lLqNnqLt9bpbcAu0ex9ULa+7nGT4ap
// +YvGDqqeJJozJle0H8RR9NB6DUYP7+2DOeLue37AbiNgiAB7/t1JOMwqr9MtjsR2
// Zz8CAwEAAaAAMA0GCSqGSIb3DQEBCwUAA4IBAQC5xB3DUzeHp9sCn2hOP6s8SPn3
// p8zyI+CZAuzh1JmXfbhw4X3AClYkehChwfNWp8fHzXn47npn+Bp66f6gxTY/kWcC
// k5h0mzBUGsNYKjQi93EzsUgvWJdjp+6vZX5YKo6gGWHN52utLkCXMZ2milzhp2ip
// aWsboVKdft9N6TYm78aUC/b5jNGMUlKN6YNO3HmcGtQAGFCFTDlxP6gKigsijOvO
// Qba6jtrLWdCQxYsIssFfJFJwincY4qgfHp1RD121rBCFs5N7aGzyGQQkuUOq4QIF
// qUc9LIfHVV3ZTlKHPGNs7PwE42ew39mjHPHH66S+w1qyjWhp7zjosoHvYAVK
// -----END CERTIFICATE REQUEST-----";
//       }
      
      $serial =$this->createSerial();
      $signed_csr = openssl_csr_sign($csr, $ca_cert, $ca_pkey, 365, $opt, $serial);
      // $signed_csr = openssl_csr_sign($csr, null, $pkey, 365);
      if ($signed_csr === FALSE) {
         // return FALSE;
         echo 'Failed to signed certificate signing request';
      }
      openssl_x509_export($signed_csr, $crtout);
      // dd($var);
      // $crt_pem = fopen("crt.pem", "w") or die("Unable to open file!");
      // fwrite($crt_pem, $crtout);
      // fclose($crt_pem);

      $certificate_chain = $this->createCertificateChain($ca_cert_pem, $crtout);
      $valid_start=time();
      $valid_end= strtotime('+1 years', $valid_start);
      // dd($valid_start . "\n" . $valid_end);
      $this->saveCertificate($pubKey, $crtout, $certificate_chain, $serial, $valid_start, $valid_end, hwid:$request->hwid);
      
      return response()->json([
         "user_certificate" => $crtout,
         "certificate_chain" => $certificate_chain,
      ], 200);
      // return ($crtout);
   }

   private function saveCertificate($public_key, $certificate, $certificate_chain, $certificate_srl, $valid_start, $valid_end, $hwid = null ){
      $device = Device::where('hwid', $hwid)->first();

      $cert = new Certificate;
      
      if($hwid != null) $cert->device_id = $device->id;
      $cert->public_key = $public_key;
      $cert->certificate = $certificate;
      $cert->certificate_chain = $certificate_chain;
      $cert->certificate_srl = $certificate_srl;
      $cert->valid_start = $valid_start;
      $cert->valid_end = $valid_end;
      $cert->save();
   }

   private function createSerial(){
      // $lastCert = DB::table('certificates')->latest('id')->first();
      $lastCertId = Certificate::all()->last()->id;
      $serial = $lastCertId + 1;
      // dd($serial);
      return $serial;
   }

   private function createCertificateChain($ca_cert, $user_cert){
      $cert_chain = $user_cert . $ca_cert;
      return $cert_chain;
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
}
