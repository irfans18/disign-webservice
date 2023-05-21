<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
   use HasFactory;

   /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
      'device_id',
      'public_key',
      'certificate',
      'certificate_srl',
      'is_revoked',
      'revoked_at',
      'revokation_detail',
      'valid_start',
      'valid_end',
   ];

   public function device()
   {
      return $this->belongsTo(Device::class);
   }
}
