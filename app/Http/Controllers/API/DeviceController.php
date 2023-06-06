<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{

   public function correspondentCert()
   {
      $devices = Device::with(['certificates' => function ($query) {
         $query->where('is_revoked', false)
            ->where('valid_end', '>=', now()); // Filter active certificates
      }])->get();

      return response()->json($devices);
   }

   // public function showUserDevices()
   // {
   //    $devices = Auth::user()->device;
   //    return response()->json($devices);
   // }

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
         return null;
      }
      $device->last_active = date("d-m-Y h:i A", time());
      return [
         'status' => "Device already registered.",
         'id' => $device->id,
         'device_name' => $device->device_name,
         'hwid' => $device->hwid,
         'last_active' => $device->last_active,
      ];
      $device->update();
   }

   public function store($user_id, $hwid, $device_name)
   {
      // Create the device
      $device = Device::create([
         'user_id' => $user_id,
         'hwid' => $hwid,
         'device_name' => $device_name,
         'last_active' => date("d-m-Y h:i A", time()),

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
      $device = Auth::user()->devices()->where('hwid', $request->hwid)->first();
      if ($device == null) {
         $device = Device::create([
            'user_id' => Auth::user()->id,
            'hwid' => $request->hwid,
            'device_name' => $request->device_name,
            'last_active' => date("d-m-Y h:i A", time()),
         ]);
         return response()->json(['device' => $device, 'message' => 'Device successfully registered'], 201);
      }
      return response()->json(['device' => $device, 'message' => 'Device already registered'], 422);
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
