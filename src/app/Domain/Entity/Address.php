<?php

namespace Relmans\Domain\Entity;

class Address implements \JsonSerializable
{
    private string $line1;
    private ?string $line2;
    private ?string $line3;
    private ?string $town;
    private ?string $county;
    private string $postCode;

    public function __construct(
        string $line1,
        ?string $line2,
        ?string $line3,
        ?string $town,
        ?string $county,
        string $postCode
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->line3 = $line3;
        $this->town = $town;
        $this->county = $county;
        $this->postCode = $postCode;
    }

    public function getLine1(): string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getLine3(): ?string
    {
        return $this->line3;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function jsonSerialize()
    {
        return (object) [
            'line1' => $this->line1,
            'line2' => $this->line2,
            'line3' => $this->line3,
            'town' => $this->town,
            'county' => $this->county,
            'postCode' => $this->postCode,
        ];
    }
}
