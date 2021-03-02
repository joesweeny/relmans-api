<?php

namespace Relmans\Domain\Factory;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Service\Payment\PaymentService;
use Relmans\Domain\Service\Payment\PaymentServiceException;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Exception\ValidationException;
use Relmans\Framework\Time\FixedClock;

class OrderFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProductReader|ObjectProphecy
     */
    private $productReader;
    /**
     * @var PaymentService|ObjectProphecy
     */
    private ObjectProphecy $paymentService;
    /**
     * @var LoggerInterface|ObjectProphecy
     */
    private ObjectProphecy $logger;
    private OrderFactory $factory;

    public function setUp(): void
    {
        $this->productReader = $this->prophesize(ProductReader::class);
        $this->paymentService = $this->prophesize(PaymentService::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->factory = new OrderFactory(
            $this->productReader->reveal(),
            $this->paymentService->reveal(),
            $this->logger->reveal(),
            new FixedClock(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'))
        );
    }

    public function test_createNewOrder_returns_a_new_Order_object()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                100,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willReturn('TRAN1');

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willReturn($this->product());

        $this->productReader->getPriceById(Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'))
            ->shouldBeCalled()
            ->willReturn($this->product()->getPrices()[0]);

        $order = $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);

        $this->assertEquals('ORDER123', $order->getId());
        $this->assertEquals('TRAN1', $order->getTransactionId());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals(OrderStatus::PENDING(), $order->getStatus());
        $this->assertEquals($orderMethod, $order->getMethod());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getUpdatedAt());

        $this->assertCount(1, $order->getItems());
        $this->assertEquals('ORDER123', $order->getItems()[0]->getOrderId());
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $order->getItems()[0]->getProductId());
        $this->assertEquals('Maris Pipers', $order->getItems()[0]->getName());
        $this->assertEquals(100, $order->getItems()[0]->getPrice());
        $this->assertEquals(500, $order->getItems()[0]->getSize());
        $this->assertEquals(Measurement::GRAMS(), $order->getItems()[0]->getMeasurement());
        $this->assertEquals(10, $order->getItems()[0]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getItems()[0]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getItems()[0]->getUpdatedAt());
    }

    public function test_createNewOrder_throws_a_ValidationException_if_the_order_is_not_found_via_the_payment_service()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                100,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Not found'));

        $this->logger->error('Error fetching order from payment service: Not found', Argument::type('array'))->shouldBeCalled();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Unable to validate order number');
        $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);
    }

    public function test_createNewOrder_parses_an_empty_string_for_the_transaction_if_if_payment_service_throws_a_PaymentServiceException()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                100,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willThrow(new PaymentServiceException('Error making request'));

        $this->logger->error('Error making request', Argument::type('array'))->shouldBeCalled();

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willReturn($this->product());

        $this->productReader->getPriceById(Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'))
            ->shouldBeCalled()
            ->willReturn($this->product()->getPrices()[0]);

        $order = $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);

        $this->assertEquals('ORDER123', $order->getId());
        $this->assertEquals('', $order->getTransactionId());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals(OrderStatus::PENDING(), $order->getStatus());
        $this->assertEquals($orderMethod, $order->getMethod());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getUpdatedAt());

        $this->assertCount(1, $order->getItems());
        $this->assertEquals('ORDER123', $order->getItems()[0]->getOrderId());
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $order->getItems()[0]->getProductId());
        $this->assertEquals('Maris Pipers', $order->getItems()[0]->getName());
        $this->assertEquals(100, $order->getItems()[0]->getPrice());
        $this->assertEquals(500, $order->getItems()[0]->getSize());
        $this->assertEquals(Measurement::GRAMS(), $order->getItems()[0]->getMeasurement());
        $this->assertEquals(10, $order->getItems()[0]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getItems()[0]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-24T12:00:00+00:00'), $order->getItems()[0]->getUpdatedAt());
    }

    public function test_createNewOrder_throws_a_ValidationException_if_product_does_not_exist()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                100,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willReturn('TRAN1');

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Product not found'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Product not found');
        $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);
    }

    public function test_createNewOrder_throws_a_ValidationException_if_product_price_does_not_exist()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                100,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willReturn('TRAN1');

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willReturn($this->product());

        $this->productReader->getPriceById(Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'))
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Product price not found'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Product price not found');
        $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);
    }

    public function test_createNewOrder_throws_a_ValidationException_if_price_value_provided_does_not_match_price_value_in_database()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                1,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willReturn('TRAN1');

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willReturn($this->product());

        $this->productReader->getPriceById(Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'))
            ->shouldBeCalled()
            ->willReturn($this->product()->getPrices()[0]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Incorrect price provided for price item eb3553bf-4e93-4a76-a9e2-85c37fe9d957');
        $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);
    }

    public function test_createNewOrder_throws_a_ValidationException_if_product_and_price_are_not_associated()
    {
        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                'Templetown',
                'In the ghetto',
                'Consett',
                'Durham',
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'),
                Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                1,
                10
            ),
        ];

        $this->paymentService->getTransactionId('ORDER123')
            ->shouldBeCalled()
            ->willReturn('TRAN1');

        $this->productReader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'))
            ->shouldBeCalled()
            ->willReturn($this->product());

        $this->productReader->getPriceById(Uuid::fromString( 'eb3553bf-4e93-4a76-a9e2-85c37fe9d957'))
            ->shouldBeCalled()
            ->willReturn(new ProductPrice(
                Uuid::fromString('eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                Uuid::uuid4(),
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            ));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Price eb3553bf-4e93-4a76-a9e2-85c37fe9d957 is not associated to product ea00060d-fb4a-4583-a76c-736f0c06bd86');
        $this->factory->createNewOrder('ORDER123', $customer, $orderMethod, $orderItems);
    }

    private function product(): Product
    {
        $productId = Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86');

        $prices1 = [
            new ProductPrice(
                Uuid::fromString('eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                $productId,
                100,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            ),
            new ProductPrice(
                Uuid::fromString('f09f19b8-8e1f-450d-86cc-2930ec3fa10f'),
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            ),
        ];

        return new Product(
            $productId,
            Uuid::fromString('c4f8fa24-4d63-4dd5-aa90-227aeda9d865'),
            'Maris Pipers',
            ProductStatus::IN_STOCK(),
            false,
            $prices1,
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
        );
    }
}
