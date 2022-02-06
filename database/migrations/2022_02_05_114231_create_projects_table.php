<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('slug');
            $table->string('bisnis_slug');
            $table->string('category');
            $table->string('penerbit');
            $table->string('location');
            $table->string('sistem_pengelolaan');
            $table->string('skema_bisnis');
            $table->bigInteger('total_pendanaan');
            $table->bigInteger('total_perolehan');
            $table->integer('min_invest');
            $table->integer('harga_perlembar');
            $table->integer('min_dividen');
            $table->integer('max_dividen');
            $table->string('dividen_periode');
            $table->integer('saham_dibagikan');
            $table->string('keuntungan_historis');
            $table->string('balik_modal');
            $table->string('map_link');
            $table->string('proposal_link');
            $table->text('keterangan');
            $table->text('img_url');
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
        Schema::dropIfExists('projects');
    }
}
