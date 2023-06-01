<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $devices = Device::all();
      return response()->json($devices);
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
   // public function store(Request $request)
   // {
   //    $device = Device::create($request->all());
   //    return response()->json($device, 201);
   // }

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
