<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class ArrayBinding extends Binding
{
    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        if ($this->jsonField && array_key_exists($this->jsonField, $jsonData) && $this->type) {
            $data = $jsonData[$this->jsonField];
            $values = [];

            if (is_array($data)) {
                foreach ($data as $key => $item) {
                    $values[$key] = $jsonDecoder->decodeArray($item, $this->type);
                }

                $property->set($values);
            }
        }
    }
}
