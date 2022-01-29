<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

use FlexPHP\Bundle\LocationBundle\Domain\Currency\CurrencyRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Customer;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\CustomerFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\CustomerRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Request\CreateCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Request\IndexCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Request\ReadCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Request\UpdateCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\UseCase\CreateCustomerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\UseCase\IndexCustomerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\UseCase\ReadCustomerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\UseCase\UpdateCustomerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetail;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\CreatePaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\DeletePaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\IndexPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\ReadPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\UpdatePaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\CreatePaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\DeletePaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\IndexPaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\ReadPaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\UpdatePaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Payment;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\CreatePaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\DeletePaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\ReadPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\UpdatePaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\CreatePaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\DeletePaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\ReadPaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\UpdatePaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\ProductRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\Request\ReadProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\UseCase\ReadProductUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Request\CreateVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Request\IndexVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Request\ReadVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Request\UpdateVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\UseCase\CreateVehicleUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\UseCase\IndexVehicleUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\UseCase\ReadVehicleUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\UseCase\UpdateVehicleUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Vehicle;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\VehicleFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\VehicleRepository;
use Exception;

abstract class AbstractPaysheetUseCase
{
    protected PaysheetRepository $orderRepository;

    private VehicleRepository $vehicleRepository;

    private CustomerRepository $customerRepository;

    private PaysheetDetailRepository $orderDetailRepository;

    private ProductRepository $productRepository;

    private PaymentRepository $paymentRepository;

    private CurrencyRepository $currencyRepository;

    public function __construct(
        PaysheetRepository $orderRepository,
        VehicleRepository $vehicleRepository,
        CustomerRepository $customerRepository,
        PaysheetDetailRepository $orderDetailRepository,
        ProductRepository $productRepository,
        PaymentRepository $paymentRepository,
        CurrencyRepository $currencyRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->customerRepository = $customerRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
        $this->paymentRepository = $paymentRepository;
        $this->currencyRepository = $currencyRepository;
    }

    abstract public function execute($request);

    protected function getVehicleId(CreatePaysheetRequest $request): ?int
    {
        $_vehicle = (new VehicleFactory())->make($request->vehicle);

        if ($_vehicle->id()) {
            $vehicle = $this->getVehicleById($_vehicle);
            $vehicle = $this->updateVehicle($vehicle->id(), $request);
        } elseif ($_vehicle->placa()) {
            $vehicle = $this->getVehicleByPlaca($_vehicle, $request);
        } else {
            return null;
        }

        return $vehicle->id();
    }

    protected function getVehicleById(Vehicle $_vehicle): Vehicle
    {
        $useCase = new ReadVehicleUseCase($this->vehicleRepository);

        $vehicle = ($useCase->execute(new ReadVehicleRequest($_vehicle->id())))->vehicle;

        if (!$vehicle->id()) {
            throw new Exception(\sprintf('Vehicle not found [%d]', $_vehicle->id()), 404);
        }

        return $vehicle;
    }

    protected function getVehicleByPlaca(Vehicle $_vehicle, CreatePaysheetRequest $request): Vehicle
    {
        $useCase = new IndexVehicleUseCase($this->vehicleRepository);

        $vehicles = ($useCase->execute(new IndexVehicleRequest([
            'placa' => $_vehicle->placa(),
        ], 1, 1)))->vehicles;

        if (\count($vehicles) > 0) {
            $vehicle = \end($vehicles);

            return $this->updateVehicle($vehicle->id(), $request);
        }

        return $this->createVehicle($request);
    }

    protected function createVehicle(CreatePaysheetRequest $request): Vehicle
    {
        $request->vehicle['isActive'] = true;

        $useCase = new CreateVehicleUseCase($this->vehicleRepository);

        $vehicle = ($useCase->execute(new CreateVehicleRequest($request->vehicle, $request->createdBy)))->vehicle;

        if (!$vehicle->id()) {
            throw new Exception('Vehicle not create', 404);
        }

        return $vehicle;
    }

    protected function updateVehicle(int $id, CreatePaysheetRequest $request): Vehicle
    {
        $request->vehicle['isActive'] = true;

        $useCase = new UpdateVehicleUseCase($this->vehicleRepository);

        return ($useCase->execute(new UpdateVehicleRequest($id, $request->vehicle, $request->createdBy, true)))->vehicle;
    }

    protected function getCustomerId(CreatePaysheetRequest $request): int
    {
        $_customer = (new CustomerFactory())->make($request->customer);

        if ($_customer->id()) {
            $customer = $this->getCustomerById($_customer);
            $customer = $this->updateCustomer($customer->id(), $request->customer, $request->createdBy);
        } elseif ($_customer->documentTypeId() && $_customer->documentNumber()) {
            $customer = $this->getCustomerByDocument($_customer, $request);
        } else {
            $customer = $this->createCustomer($request->customer, $request->createdBy);
        }

        return $customer->id();
    }

    protected function getCustomerById(Customer $_customer): Customer
    {
        $useCase = new ReadCustomerUseCase($this->customerRepository);

        $customer = ($useCase->execute(new ReadCustomerRequest($_customer->id())))->customer;

        if (!$customer->id()) {
            throw new Exception(\sprintf('Customer not found [%d]', $_customer->id()), 404);
        }

        return $customer;
    }

    protected function getCustomerByDocument(Customer $_customer, CreatePaysheetRequest $request): Customer
    {
        $useCase = new IndexCustomerUseCase($this->customerRepository);

        $customers = ($useCase->execute(new IndexCustomerRequest([
            'documentTypeId' => $_customer->documentTypeId(),
            'documentNumber' => $_customer->documentNumber(),
        ], 1, 1)))->customers;

        if (\count($customers) > 0) {
            $customer = \end($customers);

            return $this->updateCustomer($customer->id(), $request->customer, $request->createdBy);
        }

        return $this->createCustomer($request->customer, $request->createdBy);
    }

    protected function createCustomer(array $customer, int $userId): Customer
    {
        $useCase = new CreateCustomerUseCase($this->customerRepository);

        $customer = ($useCase->execute(new CreateCustomerRequest($customer, $userId)))->customer;

        if (!$customer->id()) {
            throw new Exception('Customer not create', 404);
        }

        return $customer;
    }

    protected function updateCustomer(int $id, array $customer, int $userId): Customer
    {
        $useCase = new UpdateCustomerUseCase($this->customerRepository);

        return ($useCase->execute(new UpdateCustomerRequest($id, $customer, $userId, true)))->customer;
    }

    protected function getPaysheetDetails(CreatePaysheetRequest $request): array
    {
        $orderDetails = [];
        $ids = $request->orderDetail['id'] ?? [];
        $productIds = $request->orderDetail['productId'] ?? [];
        $quantities = $request->orderDetail['quantity'] ?? [];
        $prices = $request->orderDetail['price'] ?? [];

        foreach ($productIds as $index => $productId) {
            $id = $ids[$index] ?? 0;
            $quantity = $quantities[$index] ?? 0;
            $price = $prices[$index] ?? 0;

            $useCase = new ReadProductUseCase($this->productRepository);

            $product = ($useCase->execute(new ReadProductRequest((int)$productId)))->product;

            if (!$product->id()) {
                throw new Exception(\sprintf('Product not found [%d]', $productId), 404);
            }

            $tax = $quantity * (($product->taxes() * $price) / 100);

            $orderDetails[] = [
                'id' => $id,
                'tax' => $this->numberFormat($tax),
                'total' => $this->numberFormat(($quantity * $price) + $tax),
                'price' => $this->numberFormat((float)$price),
                'quantity' => $quantity,
                'productId' => $product->id(),
                'taxes' => $product->taxes(),
            ];
        }

        return $orderDetails;
    }

    protected function getPayments(CreatePaysheetRequest $request): array
    {
        $payments = $this->getArrayPayments($request);

        if ($request->isDraft && \count($payments) > 0) {
            throw new Exception('Draft order not allow payments');
        }

        $payments = $this->getSortedPayments($payments);

        return $this->getFixedPayments($payments, $request->total);
    }

    protected function savePaysheetDetails(array $orderDetails, int $orderId, int $userId): void
    {
        $this->deletePaysheetDetails($orderDetails, $orderId);

        foreach ($orderDetails as $orderDetail) {
            $_orderDetail = (new PaysheetDetailFactory())->make($orderDetail);

            if ($_orderDetail->id()) {
                $_orderDetail = $this->getPaysheetDetail($_orderDetail);
                $_orderDetail = $this->updatePaysheetDetail($_orderDetail->id(), $orderDetail + ['orderId' => $orderId], $userId);
            } else {
                $orderDetail = $this->createPaysheetDetail($orderDetail, $orderId, $userId);
            }
        }
    }

    protected function getPaysheetDetail(PaysheetDetail $_orderDetail): PaysheetDetail
    {
        $useCase = new ReadPaysheetDetailUseCase($this->orderDetailRepository);

        $orderDetail = ($useCase->execute(new ReadPaysheetDetailRequest($_orderDetail->id())))->orderDetail;

        if (!$orderDetail->id()) {
            throw new Exception(\sprintf('Paysheet detail not found [%d]', $_orderDetail->id()), 404);
        }

        return $orderDetail;
    }

    protected function createPaysheetDetail(array $orderDetail, int $orderId, int $userId): PaysheetDetail
    {
        $useCase = new CreatePaysheetDetailUseCase($this->orderDetailRepository);

        $orderDetail = ($useCase->execute(new CreatePaysheetDetailRequest($orderDetail + [
            'orderId' => $orderId,
        ], $userId)))->orderDetail;

        if (!$orderDetail->id()) {
            throw new Exception('Paysheet detail not create', 404);
        }

        return $orderDetail;
    }

    protected function updatePaysheetDetail(int $id, array $orderDetail, int $userId): PaysheetDetail
    {
        $useCase = new UpdatePaysheetDetailUseCase($this->orderDetailRepository);

        return ($useCase->execute(new UpdatePaysheetDetailRequest($id, $orderDetail, $userId)))->orderDetail;
    }

    protected function deletePaysheetDetail(int $id): PaysheetDetail
    {
        $useCase = new DeletePaysheetDetailUseCase($this->orderDetailRepository);

        return ($useCase->execute(new DeletePaysheetDetailRequest($id)))->orderDetail;
    }

    protected function createPaysheetDetails(array $orderDetails, int $orderId, int $userId): void
    {
        foreach ($orderDetails as $orderDetail) {
            $this->createPaysheetDetail($orderDetail, $orderId, $userId);
        }
    }

    protected function deletePaysheetDetails(array $orderDetails, int $orderId): void
    {
        $useCase = new IndexPaysheetDetailUseCase($this->orderDetailRepository);
        $currentPaysheetDetails = $useCase->execute(new IndexPaysheetDetailRequest(['orderId' => $orderId], 1))->orderDetails;

        $validIds = \array_reduce($orderDetails, function ($result, $orderDetail) {
            $result[] = $orderDetail['id'];

            return $result;
        }, []);

        foreach ($currentPaysheetDetails as $currentPaysheetDetail) {
            if (\in_array($currentPaysheetDetail->id(), $validIds)) {
                continue;
            }

            $this->deletePaysheetDetail($currentPaysheetDetail->id());
        }
    }

    protected function getSubTotal(array $orderDetails): float
    {
        $subTotal = 0;

        foreach ($orderDetails as $orderDetail) {
            $subTotal += $orderDetail['quantity'] * $orderDetail['price'];
        }

        return $this->numberFormat($subTotal);
    }

    protected function getTotalTaxes(array $orderDetails): float
    {
        $taxes = 0;

        foreach ($orderDetails as $orderDetail) {
            $taxes += $orderDetail['tax'];
        }

        return $this->numberFormat($taxes);
    }

    protected function getTotalPaid(array $payments): float
    {
        $totalPaid = 0;

        foreach ($payments as $payment) {
            $totalPaid += $payment['amount'];
        }

        return $this->numberFormat($totalPaid);
    }

    protected function getPaidAt(CreatePaysheetRequest $request): ?string
    {
        if ($this->isPayed($request)) {
            return \date('Y-m-d H:i:s');
        }

        return null;
    }

    protected function getStatusId(CreatePaysheetRequest $request): string
    {
        if ($this->isDraft($request)) {
            return PayrollStatus::DRAFT;
        }

        if ($this->isPayed($request)) {
            return PayrollStatus::PAYED;
        }

        return PayrollStatus::PENDING;
    }

    protected function getKilometers(CreatePaysheetRequest $request): int
    {
        return empty($request->kilometers) ? 0 : (int)$request->kilometers;
    }

    protected function getKilometersToChange(CreatePaysheetRequest $request): int
    {
        return empty($request->kilometersToChange) ? 0 : (int)$request->kilometersToChange;
    }

    protected function getDiscount(CreatePaysheetRequest $request): string
    {
        return empty($request->discount) ? '0' : $request->discount;
    }

    protected function getTotal(CreatePaysheetRequest $request): float
    {
        return $this->numberFormat(($request->subtotal + $request->taxes) - $request->discount);
    }

    protected function numberFormat(float $number): float
    {
        return \round($number, 0);
    }

    protected function savePayments(array $payments, int $orderId, int $userId): void
    {
        $this->deletePayments($payments, $orderId);

        foreach ($payments as $payment) {
            $_payment = (new PaymentFactory())->make($payment);

            if ($_payment->id()) {
                $_payment = $this->getPayment($_payment);
                $_payment = $this->updatePayment($_payment->id(), $payment + ['orderId' => $orderId], $userId);
            } else {
                $payment = $this->createPayment($payment, $orderId, $userId);
            }
        }
    }

    protected function getPayment(Payment $_payment): Payment
    {
        $useCase = new ReadPaymentUseCase($this->paymentRepository, $this->currencyRepository);

        $payment = ($useCase->execute(new ReadPaymentRequest($_payment->id())))->payment;

        if (!$payment->id()) {
            throw new Exception(\sprintf('Payment not found [%d]', $_payment->id()), 404);
        }

        return $payment;
    }

    protected function createPayment(array $payment, int $orderId, int $userId): Payment
    {
        $useCase = new CreatePaymentUseCase($this->paymentRepository, $this->currencyRepository);

        $payment = ($useCase->execute(new CreatePaymentRequest($payment + [
            'orderId' => $orderId,
        ], $userId)))->payment;

        if (!$payment->id()) {
            throw new Exception('Payment not create', 404);
        }

        return $payment;
    }

    protected function updatePayment(int $id, array $payment, int $userId): Payment
    {
        $useCase = new UpdatePaymentUseCase($this->paymentRepository, $this->currencyRepository);

        return ($useCase->execute(new UpdatePaymentRequest($id, $payment, $userId)))->payment;
    }

    protected function deletePayment(int $id): Payment
    {
        $useCase = new DeletePaymentUseCase($this->paymentRepository, $this->currencyRepository);

        return ($useCase->execute(new DeletePaymentRequest($id)))->payment;
    }

    protected function createPayments(array $payments, int $orderId, int $userId): void
    {
        foreach ($payments as $payment) {
            $this->createPayment($payment, $orderId, $userId);
        }
    }

    protected function deletePayments(array $payments, int $orderId): void
    {
        $useCase = new IndexPaymentUseCase($this->paymentRepository, $this->currencyRepository);
        $currentPayments = $useCase->execute(new IndexPaymentRequest(['orderId' => $orderId], 1))->payments;

        $validIds = \array_reduce($payments, function ($result, $payment) {
            $result[] = $payment['id'];

            return $result;
        }, []);

        foreach ($currentPayments as $currentPayment) {
            if (\in_array($currentPayment->id(), $validIds)) {
                continue;
            }

            $this->deletePayment($currentPayment->id());
        }
    }

    private function isDraft(CreatePaysheetRequest $request): bool
    {
        return (bool)$request->isDraft;
    }

    private function isPayed(CreatePaysheetRequest $request): bool
    {
        return $request->totalPaid && $request->total === $request->totalPaid;
    }

    private function getArrayPayments(CreatePaysheetRequest $request): array
    {
        $payments = [];
        $ids = $request->payment['id'] ?? [];
        $currencyIds = $request->payment['currencyId'] ?? [];
        $paymentStatusIds = $request->payment['paymentStatusId'] ?? [];
        $paymentMethodIds = $request->payment['paymentMethodId'] ?? [];
        $amounts = $request->payment['amount'] ?? [];

        foreach ($amounts as $index => $amount) {
            $payments[] = [
                'id' => $ids[$index] ?? 0,
                'currencyId' => $currencyIds[$index] ?? 'COP',
                'paymentStatusId' => $paymentStatusIds[$index] ?? '00',
                'paymentMethodId' => $paymentMethodIds[$index] ?? '10',
                'amount' => $this->numberFormat((float)($amount ?? 0)),
            ];
        }

        return $payments;
    }

    private function getSortedPayments(array $payments): array
    {
        $paymentBy = [];
        $paymentByCash = [];

        foreach ($payments as $payment) {
            if ($payment['paymentMethodId'] === '10') {
                $paymentByCash[] = $payment;

                continue;
            }

            $paymentBy[] = $payment;
        }

        return \array_merge($paymentBy, $paymentByCash);
    }

    private function getFixedPayments(array $payments, float $total): array
    {
        $amount = 0;
        $complete = false;

        foreach ($payments as $index => $payment) {
            $amount += $payment['amount'];

            if (!$complete && $amount > $total) {
                $payments[$index]['amount'] = $payment['amount'] - ($amount - $total);
                $complete = true;
            } elseif ($complete) {
                unset($payments[$index]);
            }
        }

        return $payments;
    }
}
