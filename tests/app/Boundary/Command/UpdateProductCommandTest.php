<?php

namespace Relmans\Boundary\Command;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Enum\ProductStatus;

class UpdateProductCommandTest extends TestCase
{
    public function test_class_can_be_instantiated()
    {
        $command = new UpdateProductCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 'OUT_OF_SEASON');

        $this->assertEquals(Uuid::fromString('ec9d126e-1ee6-4a5a-99f4-9c1748af1714'), $command->getProductId());
        $this->assertEquals(ProductStatus::OUT_OF_SEASON(), $command->getStatus());
    }

    public function test_UnexpectedValueException_is_thrown_if_status_provided_is_not_a_valid_enum_value()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Value 'INVALID' is not part of the enum Relmans\Domain\Enum\ProductStatus");
        new UpdateProductCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 'INVALID');
    }

    public function test_InvalidArgumentException_is_thrown_if_id_provided_is_not_a_valid_uuid_string()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID string: e');
        new UpdateProductCommand('e', 'OUT_OF_SEASON');
    }
}
