<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\CreateProductCommand;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Time\FixedClock;

class CreateProductCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProductWriter|ObjectProphecy
     */
    private $productWriter;
    private CreateProductCommandHandler $handler;

    public function setUp(): void
    {
        $this->productWriter = $this->prophesize(ProductWriter::class);
        $this->handler = new CreateProductCommandHandler(
            $this->productWriter->reveal(),
            new FixedClock(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'))
        );
    }

    public function test_handle_saves_a_Product_record_via_the_ProductWriter()
    {
        $command = new CreateProductCommand(
            '1d810936-56f2-4f4c-b25a-2c6fc8825757',
            'Bananas',
            'IN_STOCK',
            [
                (object) [
                    'value' => 100,
                    'size' => 1.5,
                    'measurement' => 'KILOGRAMS',
                ],
                (object) [
                    'value' => 200,
                    'size' => 3,
                    'measurement' => 'KILOGRAMS',
                ]
            ]
        );

        $productAssertion = Argument::that(function (Product $product) {
            $prices = $product->getPrices();

            $this->assertEquals(Uuid::fromString('1d810936-56f2-4f4c-b25a-2c6fc8825757'), $product->getCategoryId());
            $this->assertEquals('Bananas', $product->getName());
            $this->assertEquals(ProductStatus::IN_STOCK(), $product->getStatus());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $product->getCreatedAt());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $product->getUpdatedAt());

            $this->assertEquals(100, $prices[0]->getValue());
            $this->assertEquals(1.5, $prices[0]->getSize());
            $this->assertEquals(Measurement::KILOGRAMS(), $prices[0]->getMeasurement());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $prices[0]->getCreatedAt());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $prices[0]->getUpdatedAt());

            $this->assertEquals(200, $prices[1]->getValue());
            $this->assertEquals(3, $prices[1]->getSize());
            $this->assertEquals(Measurement::KILOGRAMS(), $prices[1]->getMeasurement());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $prices[1]->getCreatedAt());
            $this->assertEquals(new \DateTimeImmutable('2020-02-02T12:00:00+00:00'), $prices[1]->getUpdatedAt());
            return true;
        });

        $this->productWriter->insert($productAssertion)->shouldBeCalled();

        $this->handler->handle($command);
    }
}
