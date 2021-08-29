<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\DataPath;
use Rinsvent\DTO2Data\Attribute\PropertyPath;
use Rinsvent\DTO2Data\Transformer\Trim;

#[HelloSchema]
class HelloRequest
{
    #[Trim]
    public string $surname;
    #[DataPath('fake_age')]
    public int $age;
    public array $emails;
    public array $authors;
    public array $authors2;
    public array $authors3;
    public BuyRequest $buy;
    public BarInterface $bar;
    #[PropertyPath(path: 'uuid.id')]
    public UUID $uuid;
}
