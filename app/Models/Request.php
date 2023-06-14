<?php

namespace App\Models;

use App\Models\Certificate;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
   use HasFactory;

   const PENDING = 0;
   const APPROVED = 1;
   const REJECTED = 2;

   protected $fillable = [
      'user_id',
      'certificate_id',
      'status',
      'filepath',
      'revocation_detail',
      'revoked_at',
      'revoked_timestamp',
   ];

   protected $appends = [
      'status_name',
      'formatted_revoked_at',
      'formatted_revoked_timestamp',
   ];

   public function user()
   {
      return $this->belongsTo(User::class);
   }
   public function certificate()
   {
      return $this->belongsTo(Certificate::class);
   }

   public function getStatusNameAttribute()
   {
      return $this->status == self::PENDING ? 'PENDING' : ($this->status == self::APPROVED ? 'APPROVED' : 'REJECTED');
   }
   public function getFormattedRevokedAtAttribute()
   {
      $revokedAt = $this->attributes['revoked_at'];

      // Check if the value is set
      if ($revokedAt) {
         return Carbon::createFromTimestamp($revokedAt)->toDateTimeString();
      }

      return null;
   }

   public function getFormattedRevokedTimestampAttribute()
   {
      $revokedTimestamp = $this->attributes['revoked_timestamp'];

      // Check if the value is set
      if ($revokedTimestamp) {
         return Carbon::createFromTimestamp($revokedTimestamp)->toDateTimeString();
      }

      return null;
   }
}
