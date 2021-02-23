<?php

namespace Relmans\Domain\Entity;

class Customer implements \JsonSerializable
{
    private string $firstName;
    private string $lastName;
    private Address $address;
    private string $phoneNumber;

    public function __construct(string $firstName, string $lastName, Address $address, string $phoneNumber)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function jsonSerialize()
    {
        return (object) [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'address' => json_encode($this->address),
            'phoneNumber' => $this->phoneNumber,
        ];
    }
}
