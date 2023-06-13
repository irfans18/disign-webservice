<?php

namespace App\Models;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
   use HasFactory;

   const PENDING = 0;
   const APRROVE = 1;
   const REJECT = 2;

   protected $fillable = [
      'certificate_id',
      'status',
      'filepath',
      'revocation_detail',
      'revoked_at',
      'revoked_timestamp',
   ];

   protected $appends = [
      'status_name',
   ];

   public function certificate()
   {
      return $this->belongsTo(Certificate::class);
   }

   public function getStatusNameAttribute()
   {
      return $this->status == self::PENDING ? 'PENDING' : ($this->status == self::APRROVE ? 'APPROVE' : 'REJECT');
   }
}
