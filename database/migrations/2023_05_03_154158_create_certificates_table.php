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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('public_key');
            $table->longText('certificate');
            $table->longText('certificate_chain');
            $table->string('certificate_srl');
            $table->boolean('is_revoked')->default(false);
            $table->string('revoked_at')->nullable();
            $table->string('revoked_timestamp')->nullable();
            $table->string('revokation_detail')->nullable();
            $table->string('valid_start');
            $table->string('valid_end');
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
        Schema::dropIfExists('certificates');
    }
};
