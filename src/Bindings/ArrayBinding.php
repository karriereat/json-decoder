<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class ArrayBinding extends Binding
{
    /**
     * {@inheritdoc}
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        if (array_key_exists($this->jsonField, $jsonData)) {
            $data = $jsonData[$this->jsonField];
            $values = [];

            if (is_array($data)) {
                foreach ($data as $item) {
                    $values[] = $jsonDecoder->decodeArray($item, $this->type);
                }

                $propertyAccessor->set($values);
            }
        }
    }
}
