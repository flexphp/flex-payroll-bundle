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

use Exception;
use FlexPHP\Bundle\LocationBundle\Domain\Currency\CurrencyRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\CreateAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\IndexAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\UpdateAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\CreateAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\IndexAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\ReadAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\UpdateAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\CreateEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\IndexEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\UpdateEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\CreateEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\IndexEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\UpdateEmployeeUseCase;
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
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\ProductRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\Request\ReadProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\UseCase\ReadProductUseCase;

abstract class AbstractPaysheetUseCase
{
    protected PaysheetRepository $paysheetRepository;

    private EmployeeRepository $employeeRepository;

    private AgreementRepository $agreementRepository;

    // private PaysheetDetailRepository $paysheetDetailRepository;

//     private ProductRepository $productRepository;

//     private PaymentRepository $paymentRepository;

//     private CurrencyRepository $currencyRepository;

    public function __construct(
        PaysheetRepository $paysheetRepository,
        EmployeeRepository $employeeRepository,
        AgreementRepository $agreementRepository
        // PaysheetDetailRepository $paysheetDetailRepository,
        // ProductRepository $productRepository,
        // PaymentRepository $paymentRepository,
        // CurrencyRepository $currencyRepository
    ) {
        $this->paysheetRepository = $paysheetRepository;
        $this->employeeRepository = $employeeRepository;
        $this->agreementRepository = $agreementRepository;
        // $this->paysheetDetailRepository = $paysheetDetailRepository;
        // $this->productRepository = $productRepository;
        // $this->paymentRepository = $paymentRepository;
        // $this->currencyRepository = $currencyRepository;
    }

    abstract public function execute($request);

    protected function getAgreementId(CreatePaysheetRequest $request): ?int
    {
        $_agreement = (new AgreementFactory())->make($request->agreement);

        if ($_agreement->id()) {
            $agreement = $this->getAgreementById($_agreement);
            $agreement = $this->updateAgreement($agreement->id(), $request);
        } else {
            $agreement = $this->createAgreement($request);
        }

        return $agreement->id();
    }

    protected function getAgreementById(Agreement $_agreement): Agreement
    {
        $useCase = new ReadAgreementUseCase($this->agreementRepository);

        $agreement = ($useCase->execute(new ReadAgreementRequest($_agreement->id())))->agreement;

        if (!$agreement->id()) {
            throw new Exception(\sprintf('Agreement not found [%d]', $_agreement->id()), 404);
        }

        return $agreement;
    }

    protected function createAgreement(CreatePaysheetRequest $request): Agreement
    {
        $useCase = new CreateAgreementUseCase($this->agreementRepository);

        $agreement = ($useCase->execute(new CreateAgreementRequest($request->agreement, $request->createdBy)))->agreement;

        if (!$agreement->id()) {
            throw new Exception('Agreement not create', 404);
        }

        return $agreement;
    }

    protected function updateAgreement(int $id, CreatePaysheetRequest $request): Agreement
    {
        $useCase = new UpdateAgreementUseCase($this->agreementRepository);

        return ($useCase->execute(new UpdateAgreementRequest($id, $request->agreement, $request->createdBy, true)))->agreement;
    }

    protected function getEmployeeId(CreatePaysheetRequest $request): int
    {
        $_employee = (new EmployeeFactory())->make($request->employee);

        if ($_employee->id()) {
            $employee = $this->getEmployeeById($_employee);
            $employee = $this->updateEmployee($employee->id(), $request->employee, $request->createdBy);
        } elseif ($_employee->documentTypeId() && $_employee->documentNumber()) {
            $employee = $this->getEmployeeByDocument($_employee, $request);
        } else {
            $employee = $this->createEmployee($request->employee, $request->createdBy);
        }

        return $employee->id();
    }

    protected function getEmployeeById(Employee $_employee): Employee
    {
        $useCase = new ReadEmployeeUseCase($this->employeeRepository);

        $employee = ($useCase->execute(new ReadEmployeeRequest($_employee->id())))->employee;

        if (!$employee->id()) {
            throw new Exception(\sprintf('Employee not found [%d]', $_employee->id()), 404);
        }

        return $employee;
    }

    protected function getEmployeeByDocument(Employee $_employee, CreatePaysheetRequest $request): Employee
    {
        $useCase = new IndexEmployeeUseCase($this->employeeRepository);

        $employees = ($useCase->execute(new IndexEmployeeRequest([
            'documentTypeId' => $_employee->documentTypeId(),
            'documentNumber' => $_employee->documentNumber(),
        ], 1, 1)))->employees;

        if (\count($employees) > 0) {
            $employee = \end($employees);

            return $this->updateEmployee($employee->id(), $request->employee, $request->createdBy);
        }

        return $this->createEmployee($request->employee, $request->createdBy);
    }

    protected function createEmployee(array $employee, int $userId): Employee
    {
        $useCase = new CreateEmployeeUseCase($this->employeeRepository);

        $employee = ($useCase->execute(new CreateEmployeeRequest($employee, $userId)))->employee;

        if (!$employee->id()) {
            throw new Exception('Employee not create', 404);
        }

        return $employee;
    }

    protected function updateEmployee(int $id, array $employee, int $userId): Employee
    {
        $useCase = new UpdateEmployeeUseCase($this->employeeRepository);

        return ($useCase->execute(new UpdateEmployeeRequest($id, $employee, $userId, true)))->employee;
    }

    protected function getDetails(CreatePaysheetRequest $request): array
    {
        return [
            'accrued' => $request->accrued,
            'deduction' => $request->deduction,
        ];
    }

    protected function getPayments(CreatePaysheetRequest $request): array
    {
        $payments = $this->getArrayPayments($request);

        if ($request->isDraft && \count($payments) > 0) {
            throw new Exception('Draft paysheet not allow payments');
        }

        $payments = $this->getSortedPayments($payments);

        return $this->getFixedPayments($payments, $request->total);
    }

    protected function savePaysheetDetails(array $paysheetDetails, int $paysheetId, int $userId): void
    {
        $this->deletePaysheetDetails($paysheetDetails, $paysheetId);

        foreach ($paysheetDetails as $paysheetDetail) {
            $_paysheetDetail = (new PaysheetDetailFactory())->make($paysheetDetail);

            if ($_paysheetDetail->id()) {
                $_paysheetDetail = $this->getPaysheetDetail($_paysheetDetail);
                $_paysheetDetail = $this->updatePaysheetDetail($_paysheetDetail->id(), $paysheetDetail + ['paysheetId' => $paysheetId], $userId);
            } else {
                $paysheetDetail = $this->createPaysheetDetail($paysheetDetail, $paysheetId, $userId);
            }
        }
    }

    protected function getPaysheetDetail(PaysheetDetail $_paysheetDetail): PaysheetDetail
    {
        $useCase = new ReadPaysheetDetailUseCase($this->paysheetDetailRepository);

        $paysheetDetail = ($useCase->execute(new ReadPaysheetDetailRequest($_paysheetDetail->id())))->paysheetDetail;

        if (!$paysheetDetail->id()) {
            throw new Exception(\sprintf('Paysheet detail not found [%d]', $_paysheetDetail->id()), 404);
        }

        return $paysheetDetail;
    }

    protected function createPaysheetDetail(array $paysheetDetail, int $paysheetId, int $userId): PaysheetDetail
    {
        $useCase = new CreatePaysheetDetailUseCase($this->paysheetDetailRepository);

        $paysheetDetail = ($useCase->execute(new CreatePaysheetDetailRequest($paysheetDetail + [
            'paysheetId' => $paysheetId,
        ], $userId)))->paysheetDetail;

        if (!$paysheetDetail->id()) {
            throw new Exception('Paysheet detail not create', 404);
        }

        return $paysheetDetail;
    }

    protected function updatePaysheetDetail(int $id, array $paysheetDetail, int $userId): PaysheetDetail
    {
        $useCase = new UpdatePaysheetDetailUseCase($this->paysheetDetailRepository);

        return ($useCase->execute(new UpdatePaysheetDetailRequest($id, $paysheetDetail, $userId)))->paysheetDetail;
    }

    protected function deletePaysheetDetail(int $id): PaysheetDetail
    {
        $useCase = new DeletePaysheetDetailUseCase($this->paysheetDetailRepository);

        return ($useCase->execute(new DeletePaysheetDetailRequest($id)))->paysheetDetail;
    }

    protected function createPaysheetDetails(array $paysheetDetails, int $paysheetId, int $userId): void
    {
        foreach ($paysheetDetails as $paysheetDetail) {
            $this->createPaysheetDetail($paysheetDetail, $paysheetId, $userId);
        }
    }

    protected function deletePaysheetDetails(array $paysheetDetails, int $paysheetId): void
    {
        $useCase = new IndexPaysheetDetailUseCase($this->paysheetDetailRepository);
        $currentPaysheetDetails = $useCase->execute(new IndexPaysheetDetailRequest(['paysheetId' => $paysheetId], 1))->paysheetDetails;

        $validIds = \array_reduce($paysheetDetails, function ($result, $paysheetDetail) {
            $result[] = $paysheetDetail['id'];

            return $result;
        }, []);

        foreach ($currentPaysheetDetails as $currentPaysheetDetail) {
            if (\in_array($currentPaysheetDetail->id(), $validIds)) {
                continue;
            }

            $this->deletePaysheetDetail($currentPaysheetDetail->id());
        }
    }

    private function getTotalByType(string $type, array $items, array $details): float
    {
        $totalByType = 0;

        foreach ($items as $item => $fields) {
            foreach ($fields as $field) {
                foreach ($details[$type][$item] as $detail) {
                    $totalByType += !empty($detail[$field]) ? $detail[$field] : 0;
                }
            }
        }

        return $totalByType;
    }

    protected function getTotalAccrued(array $paysheetDetails): float
    {
        $items = [
            'basic' => [
                'amount',
            ],
            'transport' => [
                'amount',
                'viaticSalary',
                'viaticNoSalary',
            ],
            'vacation' => [
                'amount',
            ],
            'bonus' => [
                'amount',
                'noSalary',
            ],
            'cessation' => [
                'amount',
                'noSalary',
            ],
            'support' => [
                'amount',
                'noSalary',
            ],
            'endowment' => [
                'amount',
            ],
        ];

        $totalAccrued = $this->getTotalByType('accrued', $items, $paysheetDetails);

        return $this->numberFormat($totalAccrued);
    }

    protected function getTotalDeduction(array $paysheetDetails): float
    {
        $items = [
            'health' => [
                'amount',
            ],
            'pension' => [
                'amount',
            ],
        ];

        $totalDeduction = $this->getTotalByType('deduction', $items, $paysheetDetails);

        return $this->numberFormat($totalDeduction);
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
            return PaysheetStatus::DRAFT;
        }

        if ($this->isPayed($request)) {
            return PaysheetStatus::PAYED;
        }

        return PaysheetStatus::PENDING;
    }

//     protected function getKilometers(CreatePaysheetRequest $request): int
//     {
//         return empty($request->kilometers) ? 0 : (int)$request->kilometers;
//     }

//     protected function getKilometersToChange(CreatePaysheetRequest $request): int
//     {
//         return empty($request->kilometersToChange) ? 0 : (int)$request->kilometersToChange;
//     }

//     protected function getDiscount(CreatePaysheetRequest $request): string
//     {
//         return empty($request->discount) ? '0' : $request->discount;
//     }

    protected function getTotal(CreatePaysheetRequest $request): float
    {
        return $this->numberFormat($request->totalAccrued + $request->totalDeduction);
    }

    protected function numberFormat(float $number): float
    {
        return \round($number, 0);
    }

    protected function savePayments(array $payments, int $paysheetId, int $userId): void
    {
        $this->deletePayments($payments, $paysheetId);

        foreach ($payments as $payment) {
            $_payment = (new PaymentFactory())->make($payment);

            if ($_payment->id()) {
                $_payment = $this->getPayment($_payment);
                $_payment = $this->updatePayment($_payment->id(), $payment + ['paysheetId' => $paysheetId], $userId);
            } else {
                $payment = $this->createPayment($payment, $paysheetId, $userId);
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

    protected function createPayment(array $payment, int $paysheetId, int $userId): Payment
    {
        $useCase = new CreatePaymentUseCase($this->paymentRepository, $this->currencyRepository);

        $payment = ($useCase->execute(new CreatePaymentRequest($payment + [
            'paysheetId' => $paysheetId,
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

    protected function createPayments(array $payments, int $paysheetId, int $userId): void
    {
        foreach ($payments as $payment) {
            $this->createPayment($payment, $paysheetId, $userId);
        }
    }

    protected function deletePayments(array $payments, int $paysheetId): void
    {
        $useCase = new IndexPaymentUseCase($this->paymentRepository, $this->currencyRepository);
        $currentPayments = $useCase->execute(new IndexPaymentRequest(['paysheetId' => $paysheetId], 1))->payments;

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
