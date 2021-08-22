<?php

namespace Rinsvent\DTO2Data\Tests\Converter;

use Rinsvent\DTO2Data\Dto2DataConverter;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Author;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\Bar;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\BuyRequest;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\HelloRequest;

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
        $buy = new BuyRequest();
        $buy->phrase = 'Buy buy!!!';
        $buy->length = 10;
        $buy->isFirst = true;
        $helloRequest->buy = $buy;
        $bar = new Bar();
        $bar->barField = 32;
        $helloRequest->bar = $bar;

        $dto = $dto2DataConverter->convert($helloRequest);

//        $this->assertEquals('asdf', $dto->surname);
//        $this->assertEquals(3, $dto->age);
//        $this->assertEquals([
//            'sfdgsa',
//            'af234f',
//            'asdf33333'
//        ], $dto->emails);
//        $this->assertInstanceOf(BuyRequest::class, $dto->buy);
//        $this->assertEquals('Buy buy!!!', $dto->buy->phrase);
//        $this->assertEquals(10, $dto->buy->length);
//        $this->assertEquals(true, $dto->buy->isFirst);
//
//        $this->assertCount(2, $dto->authors);
//        $this->assertEquals('Tolkien', $dto->authors[0]->name);
//        $this->assertEquals('Sapkovsky', $dto->authors[1]->name);
//
//        $this->assertInstanceOf(Bar::class, $dto->bar);
//        $this->assertIsFloat($dto->bar->barField);
//        $this->assertEquals(32.0, $dto->bar->barField);
    }
}
