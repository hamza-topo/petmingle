<?php

use App\Enums\NewsLetter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_letters', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment(
                NewsLetter::MOBILE . ':push,' . NewsLetter::EMAIL . ':email,' . NewsLetter::ALL . ':bouth'
            );
            $table->integer('species_id')->nullable()->comment('NULL:all species included');
            $table->string('title');
            $table->longText('content');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('news_letters');
    }
}
