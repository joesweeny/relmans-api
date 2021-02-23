<?php

namespace Relmans\Domain\Entity;

class Customer implements \JsonSerializable
{
    private string $firstName;
    private string $lastName;
    private Address $address;
    private string $phoneNumber;
    private string $email;

    public function __construct(
        string $firstName,
        string $lastName,
        Address $address,
        string $phoneNumber,
        string $email
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function jsonSerialize()
    {
        return (object) [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'address' => $this->address->jsonSerialize(),
            'phone' => $this->phoneNumber,
            'email' => $this->email,
        ];
    }
}
