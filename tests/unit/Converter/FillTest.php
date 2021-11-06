<?php

namespace Rinsvent\DTO2Data\Tests\Converter;

use Rinsvent\DTO2Data\Dto2DataConverter;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Author;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Bar;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\BuyRequest;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Collection;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\CollectionItem;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\HelloRequest;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\UUID;

class FillTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSuccessFillRequestData()
    {
        $dto2DataConverter = new Dto2DataConverter();
        $helloRequest = new HelloRequest;
        $helloRequest->surname = '   asdf';
        $helloRequest->age = 3;
        $helloRequest->emails =[
            'sfdgsa',
            'af234f',
            'asdf33333'
        ];
        $author1 = new Author();
        $author1->name = 'Tolkien';
        $author2 = new Author();
        $author2->name = 'Sapkovsky';
        $helloRequest->authors = [
            $author1,
            $author2
        ];
        $helloRequest->authors2 = [
            [
                "name" => "Tolkien"
            ],
            [
                "name" => "Sapkovsky"
            ]
        ];
        $helloRequest->authors3 = [
            [
                "name" => "Tolkien"
            ],
            [
                "name" => "Sapkovsky"
            ]
        ];
        $buy = new BuyRequest();
        $buy->phrase = 'Buy buy!!!';
        $buy->length = 10;
        $buy->isFirst = true;
        $helloRequest->buy = $buy;
        $bar = new Bar();
        $bar->barField = 32;
        $helloRequest->bar = $bar;
        $helloRequest->uuid = new UUID('qwerqw-qwerqwe-werqw-qwerqw');
        $collection = new Collection();
        $collection->items = [
            new CollectionItem('3'),
            new CollectionItem('1'),
            new CollectionItem('2'),
        ];
        $helloRequest->collection = $collection;
        $helloRequest->createdAt = new \DateTimeImmutable('2020-05-21 13:36:22');

        $dto = $dto2DataConverter->convert($helloRequest);
        // codecept_debug(json_encode($dto));

        $this->assertEquals([
            "surname" => "asdf",
            "fake_age" => 3,
            "emails" => [
                "sfdgsa",
                "af234f",
                "asdf33333"
            ],
            "authors" => [
                [
                    "name" => "Tolkien"
                ],
                [
                    "name" => "Sapkovsky"
                ]
            ],
            "authors2" => [
                [
                    "name" => "Tolkien"
                ],
                [
                    "name" => "Sapkovsky"
                ]
            ],
            "authors3" => [],
            "buy" => [
                "phrase" => "Buy buy!!!",
                "length" => 10,
                "isFirst" => true
            ],
            "bar" => [
                "barField" => 32
            ],
            'uuid' => 'qwerqw-qwerqwe-werqw-qwerqw',
            'collection' => [
                [
                    'value' => '3',
                ],
                [
                    'value' => '1',
                ],
                [
                    'value' => '2',
                ],
            ],
            'createdAt' => '2020-05-21T13:36:22+00:00'
        ], $dto);
    }
}
