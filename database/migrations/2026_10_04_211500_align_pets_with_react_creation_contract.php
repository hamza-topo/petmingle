<?php

use App\Enums\Pet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->tinyInteger('sexe')->nullable()->change();
            $table->string('color', 15)->nullable()->change();
            $table->text('about')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('pets')
            ->whereNull('sexe')
            ->update(['sexe' => Pet::FEMALE]);

        DB::table('pets')
            ->whereNull('color')
            ->update(['color' => '']);

        DB::table('pets')
            ->whereNull('about')
            ->update(['about' => '']);

        Schema::table('pets', function (Blueprint $table) {
            $table->tinyInteger('sexe')->nullable(false)->change();
            $table->string('color', 15)->nullable(false)->change();
            $table->text('about')->nullable(false)->change();
        });
    }
};
