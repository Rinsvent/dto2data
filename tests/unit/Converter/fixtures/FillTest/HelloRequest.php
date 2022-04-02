<?php

namespace Rinsvent\DTO2Data\Tests\unit\Converter\fixtures\FillTest;

use Rinsvent\DTO2Data\Attribute\PropertyPath;
use Rinsvent\Transformer\Transformer\DateTimeFormat;
use Rinsvent\Transformer\Transformer\ToString;
use Rinsvent\Transformer\Transformer\Trim;

#[HelloSchema]
class HelloRequest
{
    #[Trim]
    public string $surname;
    public int $age;
    public array $emails;
    public array $authors;
    public array $authors2;
    public array $authors3;
    public BuyRequest $buy;
    public BarInterface $bar;
    #[ToString()]
    public UUID $uuid;
    public Collection $collection;
    #[DateTimeFormat]
    public \DateTimeImmutable $createdAt;

    #[Trim]
    private string $psurname;
    private int $page;
    private array $pemails;
    private array $pauthors;
    private array $pauthors2;
    private array $pauthors3;
    private BuyRequest $pbuy;
    private BarInterface $pbar;
    #[ToString()]
    private UUID $puuid;
    private Collection $pcollection;
    #[DateTimeFormat]
    private \DateTimeImmutable $pcreatedAt;

    public function getPsurname(): string
    {
        return $this->psurname;
    }

    public function setPsurname(string $psurname): self
    {
        $this->psurname = $psurname;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getPemails(): array
    {
        return $this->pemails;
    }

    public function setPemails(array $pemails): self
    {
        $this->pemails = $pemails;
        return $this;
    }

    public function getPauthors(): array
    {
        return $this->pauthors;
    }

    public function setPauthors(array $pauthors): self
    {
        $this->pauthors = $pauthors;
        return $this;
    }

    public function getPauthors2(): array
    {
        return $this->pauthors2;
    }

    public function setPauthors2(array $pauthors2): self
    {
        $this->pauthors2 = $pauthors2;
        return $this;
    }

    public function getPauthors3(): array
    {
        return $this->pauthors3;
    }

    public function setPauthors3(array $pauthors3): self
    {
        $this->pauthors3 = $pauthors3;
        return $this;
    }

    public function getPbuy(): BuyRequest
    {
        return $this->pbuy;
    }

    public function setPbuy(BuyRequest $pbuy): self
    {
        $this->pbuy = $pbuy;
        return $this;
    }

    public function getPuuid(): UUID
    {
        return $this->puuid;
    }

    public function setPuuid(UUID $puuid): self
    {
        $this->puuid = $puuid;
        return $this;
    }

    public function getPbar(): BarInterface
    {
        return $this->pbar;
    }

    public function setPbar(BarInterface $pbar): self
    {
        $this->pbar = $pbar;
        return $this;
    }

    public function getPcollection(): Collection
    {
        return $this->pcollection;
    }

    public function setPcollection(Collection $pcollection): self
    {
        $this->pcollection = $pcollection;
        return $this;
    }

    public function getPcreatedAt(): \DateTimeImmutable
    {
        return $this->pcreatedAt;
    }

    public function setPcreatedAt(\DateTimeImmutable $pcreatedAt): self
    {
        $this->pcreatedAt = $pcreatedAt;
        return $this;
    }

    #[Trim]
    public function getPdevdo(): string
    {
        return '       getPdevdo';
    }
}
