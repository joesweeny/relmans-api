<?php

namespace Relmans\Boundary\Command;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateProductPriceCommandTest extends TestCase
{
    public function test_class_can_be_instantiated()
    {
        $command = new UpdateProductPriceCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 100);

        $this->assertEquals(Uuid::fromString('ec9d126e-1ee6-4a5a-99f4-9c1748af1714'), $command->getPriceId());
        $this->assertEquals(100, $command->getValue());
    }

    public function test_InvalidArgumentException_is_thrown_if_id_provided_is_not_a_valid_uuid_string()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID string: e');
        new UpdateProductPriceCommand('e', 100);
    }

    public function test_UnexpectedValueException_is_thrown_if_value_provided_is_less_than_or_equal_to_zero()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("'value' field cannot be zero or less");
        new UpdateProductPriceCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', -1);
    }
}