<?php

namespace app\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\Handler\UpdateProductCommandHandler;
use Relmans\Boundary\Command\UpdateProductCommand;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Exception\NotFoundException;

class UpdateProductCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProductWriter|ObjectProphecy
     */
    private $writer;
    private UpdateProductCommandHandler $handler;

    public function setUp(): void
    {
        $this->writer = $this->prophesize(ProductWriter::class);
        $this->handler = new UpdateProductCommandHandler($this->writer->reveal());
    }

    public function test_handle_updates_a_product_status_via_ProductWriter()
    {
        $command = new UpdateProductCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 'OUT_OF_SEASON');

        $this->writer->updateProductStatus($command->getProductId(), $command->getStatus())->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_throws_a_NotFoundException_if_product_does_not_exist()
    {
        $command = new UpdateProductCommand('ec9d126e-1ee6-4a5a-99f4-9c1748af1714', 'OUT_OF_SEASON');

        $this->writer->updateProductStatus($command->getProductId(), $command->getStatus())
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Not found'));

        $this->expectException(NotFoundException::class);
        $this->handler->handle($command);
    }
}
