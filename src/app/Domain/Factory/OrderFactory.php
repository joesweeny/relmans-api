<?php

namespace Relmans\Domain\Factory;

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Service\Payment\PaymentService;
use Relmans\Domain\Service\Payment\PaymentServiceException;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Exception\ValidationException;
use Relmans\Framework\Time\Clock;

class OrderFactory
{
    private ProductReader $productReader;
    private PaymentService $paymentService;
    private LoggerInterface $logger;
    private Clock $clock;

    public function __construct(
        ProductReader $productReader,
        PaymentService $paymentService,
        LoggerInterface $logger,
        Clock $clock
    ) {
        $this->productReader = $productReader;
        $this->paymentService = $paymentService;
        $this->logger = $logger;
        $this->clock = $clock;
    }

    /**
     * @param string $orderNumber
     * @param Customer $customer
     * @param OrderMethod $method
     * @param array $items
     * @return Order
     * @throws ValidationException
     */
    public function createNewOrder(string $orderNumber, Customer $customer, OrderMethod $method, array $items): Order
    {
        return new Order(
            $orderNumber,
            $this->fetchTransactionId($orderNumber),
            $customer,
            OrderStatus::PENDING(),
            $method,
            $this->parseOrderItems($items, $orderNumber),
            $this->clock->now(),
            $this->clock->now()
        );
    }

    /**
     * @param string $orderNumber
     * @return string
     * @throws ValidationException
     */
    private function fetchTransactionId(string $orderNumber): string
    {
        try {
            $transactionId = $this->paymentService->getTransactionId($orderNumber);
        } catch (NotFoundException $e) {
            $this->logger->error("Error fetching order from payment service: {$e->getMessage()}");

            throw new ValidationException('Unable to validate order number');
        } catch (PaymentServiceException $e) {
            $this->logger->error($e->getMessage());
        }

        return $transactionId ?? '';
    }

    /**
     * @param array $items
     * @param string $orderNumber
     * @return array|OrderItem[]
     * @throws ValidationException
     */
    private function parseOrderItems(array $items, string $orderNumber): array
    {
        return array_map(function (OrderItemData $item) use ($orderNumber) {
            try {
                $product = $this->productReader->getById($item->getProductId());
            } catch (NotFoundException $e) {
                throw new ValidationException($e->getMessage());
            }

            try {
                $price = $this->productReader->getPriceById($item->getPriceId());
            } catch (NotFoundException $e) {
                throw new ValidationException($e->getMessage());
            }

            if (!$price->getProductId()->equals($product->getId())) {
                throw new ValidationException("Price {$item->getPriceId()} is not associated to product {$item->getProductId()}");
            }

            if ($price->getValue() !== $item->getPrice()) {
                throw new ValidationException("Incorrect price provided for price item {$item->getPriceId()}");
            }

            return new OrderItem(
                Uuid::uuid4(),
                $orderNumber,
                $product->getId(),
                $product->getName(),
                $price->getValue(),
                $price->getSize(),
                $price->getMeasurement(),
                $item->getQuantity(),
                $this->clock->now(),
                $this->clock->now()
            );
        }, $items);
    }
}
