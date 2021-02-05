<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\UpdateProductPriceCommand;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Exception\NotFoundException;

class UpdateProductPriceCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProductWriter|ObjectProphecy
     */
    private $writer;
    private UpdateProductPriceCommandHandler $handler;

    public function setUp(): void
    {
        $this->writer = $this->prophesize(ProductWriter::class);
        $this->handler = new UpdateProductPriceCommandHandler($this->writer->reveal());
    }

    public function test_handle_updates_a_ProductPrice_value_via_the_ProductWriter()
    {
        $command = new UpdateProductPriceCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 100);

        $this->writer->updateProductPrice($command->getPriceId(), $command->getValue())->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_throws_a_NotFoundException_if_product_price_does_not_exist()
    {
        $command = new UpdateProductPriceCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 100);

        $this->writer->updateProductPrice($command->getPriceId(), $command->getValue())
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Not found'));

        $this->expectException(NotFoundException::class);
        $this->handler->handle($command);
    }

    public function test_handle_does_not_call_ProductWriter_if_value_is_null()
    {
        $command = new UpdateProductPriceCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', null);

        $this->writer->updateProductPrice(
            Argument::type(Uuid::class),
            Argument::type('integer')
        )->shouldNotBeCalled();

        $this->handler->handle($command);
    }
}
