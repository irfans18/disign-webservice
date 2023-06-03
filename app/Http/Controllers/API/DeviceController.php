<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{

   public function showUserDevices()
   {
      $devices = Auth::user()->device;
      return response()->json($devices);
   }

   public function index()
   {
      $devices = Device::all();
      return response()->json($devices);
   }

   public function checkDevice($hwid)
   {
      $device = Device::where('hwid', $hwid)->first();
      if ($device == NULL) {
         // return response()->json(['message' => 'You need to register your device first!'], 401);
         return 'You need to register this device first!';
      }
      $device->last_active = time();
      return [
         'message' => "Device already registered.",
         'last_active' => date("d-m-Y h:i A", $device->last_active),
      ];
   }

   public function store($user_id, $hwid, $device_name)
   {
      // Create the device
      $device = Device::create([
         'user_id' => $user_id,
         'hwid' => $hwid,
         'device_name' => $device_name,
      ]);
      return $device;
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function register(Request $request)
   {
      // Validate the request data
      $validator = Validator::make($request->all(), [
         'hwid' => 'required|string',
         'device_name' => 'required|string',
      ]);

      if ($validator->fails()) {
         return response()->json(['message' => $validator->errors()], 422);
      }

      $device = Device::create([
         'user_id' => Auth::user()->id,
         'hwid' => $request->hwid,
         'device_name' => $request->device_name,
      ]);
      return response()->json(['device' => $device, 'message' => 'Device successfully registered'], 201);
   }

   /**
    * Display the specified resource.
    *
    * @param  \App\Models\Device  $device
    * @return \Illuminate\Http\Response
    */
   public function show(Device $device)
   {
      return response()->json($device);
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Models\Device  $device
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Device $device)
   {
      $device->update($request->all());
      return response()->json($device);
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\Device  $device
    * @return \Illuminate\Http\Response
    */
   public function destroy(Device $device)
   {
      $device->delete();
      return response()->json(null, 204);
   }
}
