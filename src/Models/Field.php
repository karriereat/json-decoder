<?php

namespace Karriere\JsonDecoder\Models;

class Field
{
    private $jsonField;
    private $property;
    private $type;

    public function __construct($jsonField, $propterty, $type = null)
    {
        $this->jsonField = $jsonField;
        $this->property = $propterty;
        $this->type = $type;
    }

    public function jsonField()
    {
        return $this->jsonField;
    }

    public function property()
    {
        return $this->property;
    }

    public function hasType()
    {
        return !empty($this->type);
    }

    public function type()
    {
        return $this->type;
    }
}
