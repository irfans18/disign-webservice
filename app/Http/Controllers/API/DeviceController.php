<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
   public function register($user_id, $hwid, $device_name)
   {
      // Create the device
      $device = Device::create([
         'user_id' => $user_id,
         'hwid' => $hwid,
         'device_name' => $device_name,
      ]);
   }
}
