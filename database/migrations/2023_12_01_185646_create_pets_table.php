<?php

use App\Enums\Pet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('species_id');//TODO:make it foreign key
            $table->integer('race_id');//TODO:make it foreign key
            $table->tinyInteger('age')->index();
            $table->tinyInteger('sexe')->index()->comment( Pet::FEMALE .':female;'.Pet::MALE.': male');
            $table->string('color', 15);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pets');
    }
}
