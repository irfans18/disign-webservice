<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      Device::create([
         'user_id' => 1,
         'hwid' => '0xe65a20e753d312a1',
         'device_name' => 'Server Disign',
      ]);
      Device::create([
         'user_id' => 2,
         'hwid' => '0x1',
         'device_name' => 'Testing Device',
      ]);
   }
}
