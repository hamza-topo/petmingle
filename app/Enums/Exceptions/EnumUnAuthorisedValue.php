<?php
namespace App\Enums\Exceptions;

final class  EnumUnAuthorisedValue extends \Exception {

    public function __construct(string $value)
    {
        $this->message = "UnAuthorised value $value ";
    }

}