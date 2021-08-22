[![pipeline status](https://git.rinsvent.ru/rinsvent/dto2data/badges/master/pipeline.svg)](https://git.rinsvent.ru/rinsvent/dto2data/-/commits/master)
[![coverage report](https://git.rinsvent.ru/rinsvent/dto2data/badges/master/coverage.svg)](https://git.rinsvent.ru/rinsvent/dto2data/-/commits/master)

Dto2data
===

## Установка
```php
composer require rinsvent/dto2data
```

## Пример

### Описания ДТО
```php
class BuyRequest
{
    public string $phrase;
    public int $length;
    public bool $isFirst;
}

interface BarInterface
{

}

class Bar implements BarInterface
{
    public float $barField;
}

class HelloRequest
{
    #[Trim]
    public string $surname;
    #[PropertyPath('fake_age')]
    public int $age;
    public array $emails;
    #[DTOMeta(class: Author::class)]
    public array $authors;
    public BuyRequest $buy;
    #[DTOMeta(class: Bar::class)]
    public BarInterface $bar;
}
```
### Использование
```php
use Rinsvent\DTO2Data\Dto2DataConverter;

$dto2DataConverter = new Dto2DataConverter();
$dto = $dto2DataConverter->convert([
    'surname' => '   asdf',
    'fake_age' => 3,
    'emails' => [
        'sfdgsa',
        'af234f',
        'asdf33333'
    ],
    'authors' => [
        [
            'name' => 'Tolkien',
        ],
        [
            'name' => 'Sapkovsky'
        ]
    ],
    'buy' => [
        'phrase' => 'Buy buy!!!',
        'length' => 10,
        'isFirst' => true,
        'extraData2' => '1234'
    ],
    'bar' => [
        'barField' => 32
    ],
    'extraData1' => 'qwer'
], new HelloRequest);
```

