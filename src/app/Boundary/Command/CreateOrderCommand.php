<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;

class CreateOrderCommand
{
    private string $orderNumber;
    private string $firstName;
    private string $lastName;
    private Address $address;
    private string $phone;
    private string $email;
    private OrderMethod $method;
    /**
     * @var array|OrderItemData[]
     */
    private array $items;

    public function __construct(
        string $orderNumber,
        string $firstName,
        string $lastName,
        object $address,
        string $phone,
        string $email,
        object $method,
        array $items
    ) {
        $this->orderNumber = $this->validateInput($orderNumber, 'orderNumber');
        $this->firstName = $this->validateInput($firstName, 'firstName');
        $this->lastName = $this->validateInput($lastName, 'lastName');
        $this->address = $this->validateAddress($address);
        $this->phone = $this->validateInput($phone, 'phone');
        $this->email = $this->validateInput($email, 'email');
        $this->method = $this->validateMethod($method);
        $this->items = $this->validateItems($items);
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getCustomer(): Customer
    {
        return new Customer(
            $this->firstName,
            $this->lastName,
            $this->address,
            $this->phone,
            $this->email
        );
    }

    public function getMethod(): OrderMethod
    {
        return $this->method;
    }

    /**
     * @return array|OrderItemData[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param object $address
     * @return Address
     * @throws \InvalidArgumentException
     */
    private function validateAddress(object $address): Address
    {
        $this->validateInput($address->line1 ?? '', 'address->line1');
        $this->validateInput($address->postCode ?? '', 'address->postCode');

        return new Address(
            $address->line1,
            $address->line2 ?? null,
            $address->line3 ?? null,
            $address->town ?? null,
            $address->county ?? null,
            $address->postCode
        );
    }

    /**
     * @param object $method
     * @return OrderMethod
     * @throws \InvalidArgumentException
     */
    private function validateMethod(object $method): OrderMethod
    {
        $this->validateInput($method->type ?? '', 'method->type');
        $this->validateInput($method->date ?? '', 'method->date');

        try {
            $method = new OrderMethod(
                new FulfilmentType($method->type),
                new \DateTimeImmutable($method->date),
                $method->fee ?? null
            );
        } catch (\UnexpectedValueException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Date provided is not a valid RFC3339 date');
        }

        return $method;
    }

    /**
     * @param array|object[] $items
     * @return array|OrderItemData[]
     * @throws \InvalidArgumentException
     */
    private function validateItems(array $items): array
    {
        return array_map(function (object $item) {
            return new OrderItemData(
                Uuid::fromString($item->productId ?? ''),
                Uuid::fromString($item->priceId ?? ''),
                $this->validateInput($item->price ?? 0, 'items->price'),
                $this->validateInput($item->quantity ?? 0, 'items->quantity'),
            );
        }, $items);
    }

    /**
     * @param string $value
     * @param string $field
     * @return string
     * @throws \InvalidArgumentException
     */
    private function validateInput(string $value, string $field): string
    {
        if (!$value) {
            throw new \InvalidArgumentException("'{$field}' field is required");
        }

        return $value;
    }
}
