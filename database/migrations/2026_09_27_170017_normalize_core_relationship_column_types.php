<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->unsignedBigInteger('species_id')->change();
            $table->index('species_id');
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('species_id')->change();
            $table->unsignedBigInteger('race_id')->change();

            $table->index('user_id');
            $table->index('species_id');
            $table->index('race_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->index('user_id');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->unsignedBigInteger('from')->change();
            $table->unsignedBigInteger('to')->change();

            $table->index('from');
            $table->index('to');
        });

        Schema::table('dislikes', function (Blueprint $table) {
            $table->unsignedBigInteger('from')->change();
            $table->unsignedBigInteger('to')->change();

            $table->index('from');
            $table->index('to');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('from')->change();
            $table->unsignedBigInteger('to')->change();

            $table->index('from');
            $table->index('to');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->unsignedBigInteger('from')->change();
            $table->unsignedBigInteger('to')->change();

            $table->index('from');
            $table->index('to');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('first_user_id')->change();
            $table->unsignedBigInteger('seconde_user_id')->change();

            $table->index('first_user_id');
            $table->index('seconde_user_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('conversation_id')->change();
            $table->unsignedBigInteger('sender_id')->change();
            $table->unsignedBigInteger('receiver_id')->change();

            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('receiver_id');
        });

        Schema::table('adoptions', function (Blueprint $table) {
            $table->unsignedBigInteger('from')
                ->comment('owner')
                ->change();

            $table->unsignedBigInteger('pet_id')
                ->comment('wich pet ?')
                ->change();

            $table->unsignedBigInteger('to')
                ->comment('new Owner')
                ->change();

            $table->index('from');
            $table->index('pet_id');
            $table->index('to');
        });
    }

    public function down(): void
    {
        Schema::table('adoptions', function (Blueprint $table) {
            $table->dropIndex(['from']);
            $table->dropIndex(['pet_id']);
            $table->dropIndex(['to']);

            $table->integer('from')->comment('owner')->change();
            $table->integer('pet_id')->comment('wich pet ?')->change();
            $table->integer('to')->comment('new Owner')->change();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id']);
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['receiver_id']);

            $table->integer('conversation_id')->change();
            $table->integer('sender_id')->change();
            $table->integer('receiver_id')->change();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['first_user_id']);
            $table->dropIndex(['seconde_user_id']);

            $table->integer('first_user_id')->change();
            $table->integer('seconde_user_id')->change();
        });

        foreach (['blocks', 'matches', 'dislikes', 'likes'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['from']);
                $table->dropIndex(['to']);

                $table->integer('from')->change();
                $table->integer('to')->change();
            });
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->integer('user_id')->change();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['species_id']);
            $table->dropIndex(['race_id']);

            $table->integer('user_id')->change();
            $table->integer('species_id')->change();
            $table->integer('race_id')->change();
        });

        Schema::table('races', function (Blueprint $table) {
            $table->dropIndex(['species_id']);
            $table->integer('species_id')->change();
        });
    }
};
