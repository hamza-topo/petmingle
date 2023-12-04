<?php 
namespace App\Enums\Exceptions;

final class EnumNotFoundProperty extends \Exception {

    
   

    public function __construct(string $name)
    {
        $this->message = "Property $name doesn't exist";
    }



}