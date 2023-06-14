<?php

namespace App\Models;

use App\Models\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
      'revoked_timestamp',
      'revocation_detail',
      'valid_start',
      'valid_end',
   ];

   public function device()
   {
      return $this->belongsTo(Device::class);
   }

   public function requests()
   {
      return $this->hasMany(Request::class);
   }
}
