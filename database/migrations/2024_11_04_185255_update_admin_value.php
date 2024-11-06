<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdminValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where('email', 'hamzaaitsidisaid.11@gmail.com')->first();
        if(!empty($user->id)){
            $user->update(['is_admin' => 1, 'password' => Hash::make('password')]);
        }else{
            User::create([
                'name' => 'Hamza ait sidi said',
                'email' => 'hamzaaitsidisaid.11@gmail.com',
                'password' => Hash::make('Hamza1995@??'),
                'is_admin' => true,
            ]);
        }
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
