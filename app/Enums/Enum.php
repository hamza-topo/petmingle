<?php 
namespace App\Enums;
use App\Enums\Exceptions\EnumNotFoundProperty;
use App\Enums\Exceptions\EnumUnAuthorisedValue;

abstract class Enum  {


        public function __construct(protected mixed $value)
        {
           
        }

        private static function getProperty($name)
        {
            $property =  static::class."::".strtoupper($name);
            if(defined($property))
                return constant($property);
            throw new EnumNotFoundProperty($property);

        }

        public static function __callStatic(string $name, array $arguments)
        {
            return static::getProperty($name);
        }

        public static function fromKey(string $key):Enum
        {
           return new static( static::getProperty($key) );
        }

        public static function fromValue(mixed $value)
        {   
            if(!in_array($value, array_values(static::getConstants()) )  )
                throw new EnumUnAuthorisedValue($value);
            return new static($value);
        }

        public static function getConstants() {
            $enumClass = new \ReflectionClass(static::class);
            return $enumClass->getConstants();
        }

        public function __toString():string
        {
            return (string)$this->value;
        }

        public function equal(mixed $value):bool
        {
            return $this->value === $value;
        }

        public function getValue()
        {
            return $this->value;
        }

      
}