<?php

function dd($value) {
    print_r($value);
    die();
}

include "vendor/autoload.php";

$decoder = new \Karriere\JsonDecoder\JsonDecoder();

$decoder->register(new \Karriere\JsonDecoder\Transformers\PersonTransformer());

$json = '{"id": 1, "name": "person name", "samples": [{"id": 1, "name": "sample 1"},{"id": 2, "name": "sample 2"}]}';
$json = json_decode($json, true);

$class = $decoder->decode($json, \Karriere\JsonDecoder\Models\Person::class);

dd($class);