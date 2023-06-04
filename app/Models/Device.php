<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
   use HasFactory;

   /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
      'user_id',
      'hwid',
      'device_name',
   ];

   public function user()
   {
      return $this->belongsTo(User::class);
   }

   public function certificates()
   {
      return $this->hasMany(Certificate::class);
   }

   public function activeCertificate()
   {
      return $this->hasOne(Certificate::class)->where('is_revoked', false)
                                              ->where('valid_end', '>=', time())->orderBy('valid_end', 'DESC');
   }
}
