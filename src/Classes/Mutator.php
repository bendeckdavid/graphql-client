<?php

namespace BendeckDavid\GraphqlClient\Classes;

use BadMethodCallException;
use Illuminate\Support\Str;

class Mutator {

    public function __get(string $key)
    {
        return match(true){

            method_exists($this, 'get'.Str::studly($key).'Attribute') =>
            $this->{'get'.Str::studly($key).'Attribute'}(),

            array_key_exists($key, $this->attributes) =>
            $this->attributes[$key],

            DEFAULT => null

        };
    }

    public function __call(string $method, array $arguments = []) : mixed
    {
        match(true) {

            // Set property, if exits, using withProperty naming
            Str::startsWith($method, 'with') &&
            property_exists($this, Str::camel(substr($method, 4))) =>
                $this->{Str::camel(substr($method, 4))} = $arguments[0],

            // Set attribute using withAttribute naming
            Str::startsWith($method, 'with') =>
                $this->variables[Str::camel(substr($method, 4))] = $arguments[0],

            DEFAULT => throw new BadMethodCallException("Method [$method] does not exist.")

        };

        return $this;
    }
}
