<?php

namespace Karriere\JsonDecoder;

interface Binding
{
    /**
     * executes the defined binding method on the class instance.
     *
     * @param JsonDecoder      $jsonDecoder
     * @param mixed            $jsonData
     * @param PropertyAccessor $propertyAccessor the class instance to bind to
     *
     * @return mixed
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor);

    /**
     * @return string the name of the property to bind
     */
    public function property();
}
