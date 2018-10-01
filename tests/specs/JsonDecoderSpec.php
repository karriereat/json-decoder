<?php

namespace tests\specs\Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Transformer;
use PhpSpec\ObjectBehavior;

class JsonDecoderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(false, false);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonDecoder::class);
    }

    public function it_should_allow_decoding_of_private_properties()
    {
        $this->beConstructedWith(true, false);
        $this->decodesPrivateProperties()->shouldReturn(true);
    }

    public function it_should_allow_decoding_of_protected_properties()
    {
        $this->beConstructedWith(false, true);
        $this->decodesProtectedProperties()->shouldReturn(true);
    }

    public function it_should_transform_raw_data()
    {
        $jsonString = '{"id": 1, "name": "John Doe"}';

        $response = $this->decode($jsonString, JsonDecoderSample::class);

        $response->shouldHaveType(JsonDecoderSample::class);
        $response->id->shouldBe(1);
        $response->name->shouldBe('John Doe');
    }

    public function it_should_transform_by_using_custom_transformer()
    {
        $this->register(new SampleTransformer());

        $jsonString = '{"id": 1, "firstname": "John", "lastname": "Doe"}';

        $response = $this->decode($jsonString, JsonDecoderSample::class);

        $response->shouldHaveType(JsonDecoderSample::class);
        $response->id->shouldBe(1);
        $response->name->shouldBe('John Doe');
    }

    public function it_should_transform_empty_raw_data_to_null()
    {
        $jsonString = 'null';

        $this->decode($jsonString, JsonDecoderSample::class)->shouldReturn(null);
    }

    public function it_should_be_able_to_transform_an_array_of_objects()
    {
        $jsonString = json_encode(
            [
                [
                    'id'   => 1,
                    'name' => 'John',
                ],
                [
                    'id'   => 2,
                    'name' => 'Jane',
                ],
            ]
        );

        $response = $this->decodeMultiple($jsonString, JsonDecoderSample::class);

        $response->shouldBeArray();
        $response->shouldHaveCount(2);

        $response[0]->shouldHaveType(JsonDecoderSample::class);
        $response[0]->id->shouldBe(1);
        $response[0]->name->shouldBe('John');

        $response[1]->shouldHaveType(JsonDecoderSample::class);
        $response[1]->id->shouldBe(2);
        $response[1]->name->shouldBe('Jane');
    }

    public function it_should_be_able_to_transform_raw_with_private_properties()
    {
        $this->beConstructedWith(true, false);
        $jsonString = '{"id": 1, "name": "John Doe"}';

        $response = $this->decode($jsonString, SampleWithPrivateProperties::class);

        $response->shouldHaveType(SampleWithPrivateProperties::class);
        $response->getId()->shouldBe(1);
        $response->getName()->shouldReturn('John Doe');
    }

    public function it_should_be_able_to_transform_null_values()
    {
        $this->register(new SampleTransformer());
        $response = $this->decode('null', JsonDecoderSample::class);

        $response->shouldBe(null);
    }
}

class JsonDecoderSample
{
    public $id;
    public $name;
}

class SampleWithPrivateProperties
{
    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}

class SampleTransformer implements Transformer
{
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
