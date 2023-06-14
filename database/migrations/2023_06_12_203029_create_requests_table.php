<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('requests', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
         $table->foreignId('certificate_id')->nullable()->constrained()->nullOnDelete();
         $table->integer('status')->default(0);
         $table->string('filepath')->nullable();
         $table->string('revocation_detail');
         $table->string('revoked_at')->nullable();
         $table->string('revoked_timestamp')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('requests');
   }
};
