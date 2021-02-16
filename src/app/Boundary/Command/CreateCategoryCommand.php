<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateCategoryCommand
{
    private string $name;

    /**
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name)
    {
        $this->name = $this->validateName($name);
    }

    public function getId(): UuidInterface
    {
        return Uuid::uuid4();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    private function validateName(string $name): string
    {
        if (!$name) {
            throw new \InvalidArgumentException("'name' field is required");
        }

        return $name;
    }
}
