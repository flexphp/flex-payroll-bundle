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
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\UpdatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response\UpdatePayrollResponse;

final class UpdatePayrollUseCase
{
    private PayrollRepository $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function execute(UpdatePayrollRequest $request): UpdatePayrollResponse
    {
        return new UpdatePayrollResponse($this->payrollRepository->change($request));
    }
}
