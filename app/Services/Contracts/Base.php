<?php 
namespace App\Services\Contracts;

interface Base {

    public function process(): bool;

    public function send(): bool;

    public function bulk(): mixed;

    //Normally we should thing about 2 main things via email or via push

}