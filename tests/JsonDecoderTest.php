<?php

namespace Karriere\JsonDecoder\Tests;

use DateTime;
use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Exceptions\InvalidJsonException;
use Karriere\JsonDecoder\Exceptions\NotExistingRootException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use Karriere\JsonDecoder\Tests\Fakes\Sample;
use Karriere\JsonDecoder\Transformer;
use PHPUnit\Framework\TestCase;

class JsonDecoderTest extends TestCase
{
    /** @test */
    public function it_fails_for_an_invalid_json_input()
    {
        $jsonDecoder = new JsonDecoder();

        $this->expectException(InvalidJsonException::class);

        $jsonDecoder->decode('invalid', Person::class);
    }

    /** @test */
    public function it_is_able_to_decode_json_without_a_transformer()
    {
        $jsonDecoder = new JsonDecoder();

        $person = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/personWithAddress.json'), Person::class);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->firstname());
        $this->assertEquals('Doe', $person->lastname());
        $this->assertIsArray($person->address());
        $this->assertEquals($person->address()['street'], 'Street');
        $this->assertEquals($person->address()['city'], 'City');
    }

    /** @test */
    public function it_is_able_to_decode_json_with_a_transformer()
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new class() implements Transformer {
            public function register(ClassBindings $classBindings)
            {
                $classBindings->register(new FieldBinding('address', 'address', Address::class));
                $classBindings->register(new DateTimeBinding('birthday', 'birthday'));
            }

            public function transforms()
            {
                return Person::class;
            }
        });

        $person = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/personWithAddress.json'), Person::class);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->firstname());
        $this->assertEquals('Doe', $person->lastname());
        $this->assertInstanceOf(Address::class, $person->address());
        $this->assertEquals('Street', $person->address()->street());
        $this->assertEquals('City', $person->address()->city());

        $expectedBirthday = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');
        $this->assertEquals($expectedBirthday, $person->birthday());
    }

    /** @test */
    public function it_is_able_to_decode_json_with_a_more_complex_transformer()
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new class() implements Transformer {
            public function register(ClassBindings $classBindings)
            {
                $classBindings->register(new AliasBinding('firstname', 'first-name'));
                $classBindings->register(new AliasBinding('lastname', 'last-name'));
                $classBindings->register(new FieldBinding('address', 'addr', Address::class));
                $classBindings->register(new DateTimeBinding('birthday', 'bd'));
            }

            public function transforms()
            {
                return Person::class;
            }
        });

        $person = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/personWithMapping.json'), Person::class);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->firstname());
        $this->assertEquals('Doe', $person->lastname());
        $this->assertInstanceOf(Address::class, $person->address());
        $this->assertEquals('Street', $person->address()->street());
        $this->assertEquals('City', $person->address()->city());

        $expectedBirthday = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');
        $this->assertEquals($expectedBirthday, $person->birthday());
    }

    /** @test */
    public function it_decodes_multiple_instances_of_a_type()
    {
        $jsonDecoder = new JsonDecoder();
        $persons     = $jsonDecoder->decodeMultiple(file_get_contents(__DIR__ . '/data/persons.json'), Person::class);

        $this->assertIsArray($persons);
        $this->assertCount(2, $persons);
        $this->assertEquals('John', $persons[0]->firstname());
        $this->assertEquals('Jane', $persons[1]->firstname());
    }

    /** @test */
    public function it_handles_empty_json_strings_gracefully()
    {
        $jsonDecoder = new JsonDecoder();

        $person = $jsonDecoder->decode('null', Person::class);
        $this->assertNull($person);

        $person = $jsonDecoder->decode('{}', Person::class);
        $this->assertNull($person);

        $jsonDecoder->register(new class() implements Transformer {
            public function register(ClassBindings $classBindings)
            {
                $classBindings->register(new FieldBinding('address', 'address', Address::class));
            }

            public function transforms()
            {
                return Person::class;
            }
        });

        $person = $jsonDecoder->decode('{}', Person::class);
        $this->assertNull($person);
    }

    /** @test */
    public function it_decodes_an_object_with_a_root_key()
    {
        $jsonDecoder = new JsonDecoder();

        $person = $jsonDecoder
            ->decode(file_get_contents(__DIR__ . '/data/personWithRoot.json'), Person::class, 'person');

        $this->assertEquals('John', $person->firstname());
        $this->assertEquals('Doe', $person->lastname());
    }

    /** @test */
    public function it_decodes_multiple_objects_with_a_root_key()
    {
        $jsonDecoder = new JsonDecoder();

        $persons = $jsonDecoder
            ->decodeMultiple(file_get_contents(__DIR__ . '/data/personsWithRoot.json'), Person::class, 'persons');

        $this->assertIsArray($persons);
        $this->assertCount(2, $persons);
        $this->assertEquals('John', $persons[0]->firstname());
        $this->assertEquals('Jane', $persons[1]->firstname());
    }

    /** @test */
    public function it_fails_for_not_existing_root_key()
    {
        $this->expectException(NotExistingRootException::class);

        (new JsonDecoder())
            ->decode(file_get_contents(__DIR__ . '/data/personWithRoot.json'), Person::class, 'not-existing');
    }

    /** @test */
    public function it_scans_a_class_and_generates_a_transformer()
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->scanAndRegister(Person::class);

        $person = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/personWithAddress.json'), Person::class);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John', $person->firstname());
        $this->assertEquals('Doe', $person->lastname());
        $this->assertInstanceOf(Address::class, $person->address());
        $this->assertEquals('Street', $person->address()->street());
        $this->assertEquals('City', $person->address()->city());

        $expectedBirthday = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');
        $this->assertEquals($expectedBirthday, $person->birthday());
    }

    /** @test */
    public function it_can_auto_case_from_snake_to_camel()
    {
        $jsonDecoder = new JsonDecoder(true);

        $sample = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/sampleInSnakeCase.json'), Sample::class);

        $this->assertInstanceOf(Sample::class, $sample);
        $this->assertEquals('value 1', $sample->publicProperty());
        $this->assertEquals('value 2', $sample->protectedProperty());
        $this->assertEquals('value 3', $sample->privateProperty());
    }

    /** @test */
    public function it_can_auto_case_from_kebap_to_camel()
    {
        $jsonDecoder = new JsonDecoder(true);

        $sample = $jsonDecoder->decode(file_get_contents(__DIR__ . '/data/sampleInKebapCase.json'), Sample::class);

        $this->assertInstanceOf(Sample::class, $sample);
        $this->assertEquals('value 1', $sample->publicProperty());
        $this->assertEquals('value 2', $sample->protectedProperty());
        $this->assertEquals('value 3', $sample->privateProperty());
    }
}
