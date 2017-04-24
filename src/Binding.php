<?php

namespace Karriere\JsonDecoder;

interface Binding
{
    /**
     * executes the defined binding method on the class instance
     *
     * @param JsonDecoder $jsonDecoder
     * @param mixed $jsonData
     * @param mixed $instance the class instance to bind to
     * @return mixed
     */
    public function bind($jsonDecoder, $jsonData, $instance);

    /**
     * @return string the name of the property to bind
     */
    public function property();
}
