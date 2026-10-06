<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

class UpdateAdminValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::withoutEvents(function () {
            $user = User::where(
                'email',
                'hamzaaitsidisaid.11@gmail.com'
            )->first();

            if ($user) {
                $user->update([
                    'is_admin' => 1,
                    'password' => Hash::make('password'),
                ]);

                return;
            }

            User::create([
                'name' => 'Hamza ait sidi said',
                'email' => 'hamzaaitsidisaid.11@gmail.com',
                'password' => Hash::make('Hamza1995@??'),
                'is_admin' => true,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
