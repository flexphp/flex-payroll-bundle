<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Controller;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Payroll;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetPaysheetPayrollsUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetPaysheetPaysheetDetailsUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetPaysheetPaymentsUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetPaysheetAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetail;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetPaysheetEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Payment;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\ReadPayrollRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ApiPaysheetController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_READ')", statusCode=401)
     */
    public function employee(GetPaysheetEmployeeUseCase $useCase, int $id): Response
    {
        $request = new ReadPaysheetRequest($id);

        $employee = $useCase->execute($request);

        return new JsonResponse([
            'data' => [
                'id' => $employee->id(),
                'documentTypeId' => $employee->documentTypeId(),
                'documentNumber' => $employee->documentNumber(),
                'firstName' => $employee->firstName(),
                'secondName' => $employee->secondName(),
                'firstSurname' => $employee->firstSurname(),
                'secondSurname' => $employee->secondSurname(),
                'accountNumber' => $employee->accountNumber(),
                'typeId' => $employee->typeInstance()->id(),
                'typeName' => $employee->typeInstance()->name(),
                'subTypeId' => $employee->subTypeInstance()->id(),
                'subTypeName' => $employee->subTypeInstance()->name(),
                'paymentMethodId' => $employee->paymentMethodInstance()->id(),
                'paymentMethodName' => $employee->paymentMethodInstance()->name(),
                'accountTypeId' => $employee->accountTypeInstance()->id(),
                'accountTypeName' => $employee->accountTypeInstance()->name(),
            ],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_READ')", statusCode=401)
     */
    public function agreement(GetPaysheetAgreementUseCase $useCase, int $id): Response
    {
        $request = new ReadPaysheetRequest($id);

        $agreement = $useCase->execute($request);

        return new JsonResponse([
            'data' => [
                'id' => $agreement->id(),
                'name' => $agreement->name(),
                'salary' => $agreement->salary(),
                'healthPercentage' => $agreement->healthPercentage(),
                'pensionPercentage' => $agreement->pensionPercentage(),
                'initAt' => $agreement->initAt() ? $agreement->initAt()->format('Y-m-d') : null,
                'finishAt' => $agreement->finishAt() ? $agreement->finishAt()->format('Y-m-d') : null,
                'integralSalary' => (int)$agreement->integralSalary(),
                'highRisk' => (int)$agreement->highRisk(),
                'typeId' => $agreement->type(),
                'typeName' => $agreement->typeInstance()->name(),
                'statusId' => $agreement->status(),
                'statusName' => $agreement->statusInstance()->name(),
                'periodId' => $agreement->period(),
                'periodName' => $agreement->periodInstance()->name(),
                'currencyId' => $agreement->currency(),
                'currencyName' => $agreement->currencyInstance()->name(),
            ],
        ]);
    }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ORDER_DETAIL_READ')", statusCode=401)
//      */
//     public function paysheetDetails(GetPaysheetPaysheetDetailsUseCase $useCase, int $id): Response
//     {
//         $request = new ReadPaysheetRequest($id);

//         $paysheetDetails = $useCase->execute($request);

//         $data = [];

//         /** @var PaysheetDetail $paysheetDetail */
//         foreach ($paysheetDetails as $paysheetDetail) {
//             $data[$paysheetDetail->id()] = [
//                 'id' => $paysheetDetail->id(),
//                 'quantity' => $paysheetDetail->quantity(),
//                 'productId' => $paysheetDetail->productId(),
//                 'name' => \trim($paysheetDetail->productIdInstance()->name()),
//                 'price' => $paysheetDetail->price(),
//                 'tax' => $paysheetDetail->taxes(),
//             ];
//         }

//         \ksort($data);

//         return new JsonResponse([
//             'data' => \array_values($data),
//         ]);
//     }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYMENT_READ')", statusCode=401)
//      */
//     public function payments(GetPaysheetPaymentsUseCase $useCase, int $id): Response
//     {
//         $request = new ReadPaysheetRequest($id);

//         $payments = $useCase->execute($request);

//         $data = [];

//         $payments = \array_reverse($payments);

//         /** @var Payment $payment */
//         foreach ($payments as $payment) {
//             $data[$payment->id()] = [
//                 'id' => $payment->id(),
//                 'currencyId' => $payment->currencyId(),
//                 'paymentStatusId' => $payment->paymentStatusId(),
//                 'paymentMethodId' => $payment->paymentMethodId(),
//                 'paymentMethodName' => $payment->paymentMethodIdInstance()
//                     ? $payment->paymentMethodIdInstance()->name()
//                     : null,
//                 'amount' => $payment->amount(),
//             ];
//         }

//         \ksort($data);

//         return new JsonResponse([
//             'data' => \array_values($data),
//         ]);
//     }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BILL_READ')", statusCode=401)
//      */
//     public function payrolls(GetPaysheetPayrollsUseCase $useCase, int $id): Response
//     {
//         $request = new ReadPaysheetRequest($id);

//         $payrolls = $useCase->execute($request);

//         $data = [];

//         /** @var Payroll $payroll */
//         foreach ($payrolls as $payroll) {
//             $data[] = [
//                 'id' => $payroll->id(),
//             ];
//         }

//         \ksort($data);

//         return new JsonResponse([
//             'data' => $data,
//         ]);
//     }
}
