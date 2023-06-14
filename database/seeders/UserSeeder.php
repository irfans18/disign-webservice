<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      User::create([
         'username' => 'adminsu',
         'name' => 'Super Admin',
         'location_info' => 'Surabaya',
         'email' => 'superadmin@this',
         'password' => bcrypt('adminsu@'),
         'pin' => md5('999999'),
         'role' => 30,
      ]);

      User::create([
         'username' => 'server',
         'name' => 'Irfan Shiddiq',
         'location_info' => 'Surabaya',
         'email' => 'server@this',
         'password' => bcrypt('irfan123'),
         'pin' => md5('999999'),
         'role' => 30,
      ]);

      User::create([
         'username' => 'irfan',
         'name' => 'Irfan Shiddiq',
         'location_info' => 'Surabaya',
         'email' => 'irfan@this',
         'password' => bcrypt('irfan123'),
         'pin' => md5('999999'),
         // 'role' => 30,
      ]);
   }
}
