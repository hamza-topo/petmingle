<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertNoOrphans();

        Schema::table('races', function (Blueprint $table) {
            $table->foreign('species_id')
                ->references('id')
                ->on('species')
                ->restrictOnDelete();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('species_id')
                ->references('id')
                ->on('species')
                ->restrictOnDelete();

            $table->foreign('race_id')
                ->references('id')
                ->on('races')
                ->restrictOnDelete();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        foreach (['likes', 'dislikes', 'matches'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('from')
                    ->references('id')
                    ->on('pets')
                    ->cascadeOnDelete();

                $table->foreign('to')
                    ->references('id')
                    ->on('pets')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('blocks', function (Blueprint $table) {
            $table->foreign('from')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('to')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('first_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('seconde_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('conversation_id')
                ->references('id')
                ->on('conversations')
                ->cascadeOnDelete();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('adoptions', function (Blueprint $table) {
            $table->foreign('from')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->foreign('pet_id')
                ->references('id')
                ->on('pets')
                ->restrictOnDelete();

            $table->foreign('to')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('adoptions', function (Blueprint $table) {
            $table->dropForeign(['from']);
            $table->dropForeign(['pet_id']);
            $table->dropForeign(['to']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['first_user_id']);
            $table->dropForeign(['seconde_user_id']);
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign(['from']);
            $table->dropForeign(['to']);
        });

        foreach (['matches', 'dislikes', 'likes'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['from']);
                $table->dropForeign(['to']);
            });
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['species_id']);
            $table->dropForeign(['race_id']);
        });

        Schema::table('races', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
        });
    }

    private function assertNoOrphans(): void
    {
        $relations = [
            ['races', 'species_id', 'species'],
            ['pets', 'user_id', 'users'],
            ['pets', 'species_id', 'species'],
            ['pets', 'race_id', 'races'],
            ['locations', 'user_id', 'users'],
            ['likes', 'from', 'pets'],
            ['likes', 'to', 'pets'],
            ['dislikes', 'from', 'pets'],
            ['dislikes', 'to', 'pets'],
            ['matches', 'from', 'pets'],
            ['matches', 'to', 'pets'],
            ['blocks', 'from', 'users'],
            ['blocks', 'to', 'users'],
            ['conversations', 'first_user_id', 'users'],
            ['conversations', 'seconde_user_id', 'users'],
            ['messages', 'conversation_id', 'conversations'],
            ['messages', 'sender_id', 'users'],
            ['messages', 'receiver_id', 'users'],
            ['adoptions', 'from', 'users'],
            ['adoptions', 'pet_id', 'pets'],
            ['adoptions', 'to', 'users'],
        ];

        foreach ($relations as [$childTable, $column, $parentTable]) {
            $orphans = DB::table($childTable)
                ->leftJoin(
                    $parentTable,
                    "{$childTable}.{$column}",
                    '=',
                    "{$parentTable}.id"
                )
                ->whereNull("{$parentTable}.id")
                ->count();

            if ($orphans > 0) {
                throw new RuntimeException(
                    "Cannot add foreign key {$childTable}.{$column}: {$orphans} orphaned row(s) found."
                );
            }
        }
    }
};
