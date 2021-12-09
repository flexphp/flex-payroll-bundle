<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response\FindPayrollPayrollResponse;

final class FindPayrollPayrollUseCase
{
    private PayrollRepository $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function execute(FindPayrollPayrollRequest $request): FindPayrollPayrollResponse
    {
        $payrolls = $this->payrollRepository->findPayrollsBy($request);

        return new FindPayrollPayrollResponse($payrolls);
    }
}
