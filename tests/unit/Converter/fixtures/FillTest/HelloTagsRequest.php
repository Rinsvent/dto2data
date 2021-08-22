<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\DataPath;
use Rinsvent\DTO2Data\Attribute\HandleTags;

#[HandleTags(method: 'getTags')]
class HelloTagsRequest extends HelloRequest
{
    #[DataPath('fake_age2', tags: ['surname-group'])]
    public int $age;

    public function getTags(array $tags)
    {
        return 'Surname1234' === trim(($this->surname ?? '')) ? ['surname-group'] : $tags;
    }
}
