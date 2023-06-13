<?php

namespace App\Models;

use App\Models\Device;
use App\Models\Certificate;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   use HasApiTokens, HasFactory, Notifiable;

   const USER = 0;
   const SUPER_ADMIN = 30;
   /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
      'username',
      'name',
      'location_info',
      'email',
      'password',
      'pin',
      'role',
   ];

   /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
   protected $hidden = [
      'password',
      'remember_token',
   ];

   /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
   protected $casts = [
      'email_verified_at' => 'datetime',
   ];

   public function devices()
   {
      return $this->hasMany(Device::class);
   }

   public function certificates()
   {
      return $this->hasManyThrough(Certificate::class, Device::class);
   }

   public function getRoleNameAttribute()
   {
      return $this->role == self::SUPER_ADMIN ? 'Super Admin' : 'User';
   }
}
