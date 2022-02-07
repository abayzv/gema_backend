<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('mother_name');
            $table->string('address');
            $table->string('city');
            $table->string('districts');
            $table->string('region');
            $table->integer('post_code');
            $table->string('id_card_number');
            $table->string('id_card_path');
            $table->string('selfie_path');
            $table->string('npwp_number');
            $table->string('npwp_path');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->timestamp('member_activated_at')->nullable();
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
        Schema::dropIfExists('user_details');
    }
}
