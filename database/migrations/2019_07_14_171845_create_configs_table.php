<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->index();
            $table->string('type')->default(\App\Models\Config::TYPE_INPUT);
            $table->string('name', 50);
            $table->string('slug', 50)->index();
            $table->string('desc')->nullable();
            $table->string('options')->nullable()->comment('Fill with config options, such as radio, multi-select drop-down options');
            $table->string('value', 5000)->nullable();
            $table->string('validation_rules', 255)->nullable()->comment('Fill with validation rules');
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
        Schema::dropIfExists('configs');
    }
}
