<?php

namespace BendeckDev\GraphqlClient\Classes;

use Illuminate\Support\Str;

class Mutator {
    
    public function __get($key){
        return match(true){

            method_exists($this, 'get'.Str::studly($key).'Attribute') => 
            $this->{'get'.Str::studly($key).'Attribute'}(),

            array_key_exists($key, $this->attributes) =>
            $this->attributes[$key],

            DEFAULT => null

        };
    }
    
}