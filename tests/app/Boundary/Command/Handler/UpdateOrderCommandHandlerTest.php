<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Relmans\Boundary\Command\UpdateOrderCommand;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Domain\Persistence\OrderWriterQuery;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Exception\EmailException;
use Relmans\Framework\Exception\NotFoundException;

class UpdateOrderCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var OrderWriter|ObjectProphecy
     */
    private $writer;
    /**
     * @var OrderReader|ObjectProphecy
     */
    private $reader;
    /**
     * @var EmailService|ObjectProphecy
     */
    private $emailService;
    /**
     * @var LoggerInterface|ObjectProphecy
     */
    private ObjectProphecy $logger;
    private UpdateOrderCommandHandler $handler;

    public function setUp(): void
    {
        $this->writer = $this->prophesize(OrderWriter::class);
        $this->reader = $this->prophesize(OrderReader::class);
        $this->emailService = $this->prophesize(EmailService::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->handler = new UpdateOrderCommandHandler(
            $this->writer->reveal(),
            $this->reader->reveal(),
            $this->emailService->reveal(),
            $this->logger->reveal()
        );
    }

    public function test_handle_update_order_status_and_sends_delivery_confirmation_email()
    {
        $command = new UpdateOrderCommand('ORD1234', 'ACCEPTED');

        $queryAssertion = Argument::that(function (OrderWriterQuery $query) {
            $this->assertEquals(OrderStatus::ACCEPTED(), $query->getStatus());
            return true;
        });

        $this->writer->update($command->getId(), $queryAssertion)->shouldBeCalled();

        /** @var Order|ObjectProphecy $order */
        $order = $this->prophesize(Order::class);
        $order->getMethod()->willReturn(new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable(), null));

        $this->reader->getById($command->getId())
            ->shouldBeCalled()
            ->willReturn($order->reveal());

        $this->emailService->sendDeliveryConfirmation($order)->shouldBeCalled();
        $this->emailService->sendAdminOrderReceivedEmail($order)->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_update_order_status_and_sends_collection_confirmation_email()
    {
        $command = new UpdateOrderCommand('ORD1234', 'ACCEPTED');

        $queryAssertion = Argument::that(function (OrderWriterQuery $query) {
            $this->assertEquals(OrderStatus::ACCEPTED(), $query->getStatus());
            return true;
        });

        $this->writer->update($command->getId(), $queryAssertion)->shouldBeCalled();

        /** @var Order|ObjectProphecy $order */
        $order = $this->prophesize(Order::class);
        $order->getMethod()->willReturn(new OrderMethod(FulfilmentType::COLLECTION(), new \DateTimeImmutable(), null));

        $this->reader->getById($command->getId())
            ->shouldBeCalled()
            ->willReturn($order->reveal());

        $this->emailService->sendCollectionConfirmation($order)->shouldBeCalled();
        $this->emailService->sendAdminOrderReceivedEmail($order)->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_throws_a_NotFoundException_if_thrown_by_order_writer()
    {
        $command = new UpdateOrderCommand('ORD1234', 'ACCEPTED');

        $queryAssertion = Argument::that(function (OrderWriterQuery $query) {
            $this->assertEquals(OrderStatus::ACCEPTED(), $query->getStatus());
            return true;
        });

        $this->writer->update($command->getId(), $queryAssertion)
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Not found'));

        $this->reader->getById($command->getId())->shouldNotBeCalled();

        $this->emailService->sendDeliveryConfirmation(Argument::type(Order::class))->shouldNotBeCalled();
        $this->emailService->sendCollectionConfirmation(Argument::type(Order::class))->shouldNotBeCalled();

        $this->expectException(NotFoundException::class);
        $this->handler->handle($command);
    }

    public function test_handle_logs_an_error_if_exception_thrown_by_email_service()
    {
        $command = new UpdateOrderCommand('ORD1234', 'ACCEPTED');

        $queryAssertion = Argument::that(function (OrderWriterQuery $query) {
            $this->assertEquals(OrderStatus::ACCEPTED(), $query->getStatus());
            return true;
        });

        $this->writer->update($command->getId(), $queryAssertion)->shouldBeCalled();

        /** @var Order|ObjectProphecy $order */
        $order = $this->prophesize(Order::class);
        $order->getMethod()->willReturn(new OrderMethod(FulfilmentType::COLLECTION(), new \DateTimeImmutable(), null));

        $this->reader->getById($command->getId())
            ->shouldBeCalled()
            ->willReturn($order->reveal());

        $this->emailService->sendCollectionConfirmation($order)
            ->shouldBeCalled()
            ->willThrow(new EmailException('Error sending email'));

        $this->logger->error('Error sending customer confirmation email: Error sending email')->shouldBeCalled();

        $this->handler->handle($command);
    }
}
