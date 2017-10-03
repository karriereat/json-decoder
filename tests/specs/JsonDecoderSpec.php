<?php

namespace tests\specs\Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Transformer;
use PhpSpec\ObjectBehavior;

class JsonDecoderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(false, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JsonDecoder::class);
    }

    function it_should_allow_decoding_of_private_properties()
    {
        $this->beConstructedWith(true, false);
        $this->decodesPrivateProperties()->shouldReturn(true);
    }

    function it_should_allow_decoding_of_protected_properties()
    {
        $this->beConstructedWith(false, true);
        $this->decodesProtectedProperties()->shouldReturn(true);
    }

    function it_should_transform_raw_data()
    {
        $jsonString = '{"id": 1, "name": "John Doe"}';

        $response = $this->decode($jsonString, JsonDecoderSample::class);

        $response->shouldHaveType(JsonDecoderSample::class);
        $response->id->shouldBe(1);
        $response->name->shouldBe("John Doe");
    }

    function it_should_transform_by_using_custom_transformer()
    {
        $this->register(new SampleTransformer());

        $jsonString = '{"id": 1, "firstname": "John", "lastname": "Doe"}';

        $response = $this->decode($jsonString, JsonDecoderSample::class);

        $response->shouldHaveType(JsonDecoderSample::class);
        $response->id->shouldBe(1);
        $response->name->shouldBe("John Doe");
    }

    function it_should_transform_empty_raw_data_to_null()
    {
        $jsonString = 'null';

        $this->decode($jsonString, JsonDecoderSample::class)->shouldReturn(null);
    }
}

class JsonDecoderSample {
    public $id;
    public $name;
}

class SampleTransformer implements Transformer {

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new CallbackBinding('name', function ($data) {
            return sprintf('%s %s', $data['firstname'], $data['lastname']);
        }));
    }

    public function transforms()
    {
        return JsonDecoderSample::class;
    }
}
