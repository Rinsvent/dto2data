<?php

namespace Rinsvent\DTO2Data\Tests\Converter;

use Rinsvent\DTO2Data\Dto2DataConverter;
use Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest\HelloTagsRequest;

class TagsTest extends \Codeception\Test\Unit
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

        $helloTagsRequest = new HelloTagsRequest();
        $helloTagsRequest->surname = '  Surname1234';
        $helloTagsRequest->age = 3;
        $helloTagsRequest->emails = [
            'sfdgsa',
            'af234f',
            'asdf33333'
        ];
        $tags = $dto2DataConverter->getTags($helloTagsRequest);
        $this->assertEquals(['surname-group'], $tags);
    }
}
