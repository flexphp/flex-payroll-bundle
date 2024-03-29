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

use FlexPHP\Bundle\InvoiceBundle\Domain\Payroll\Payroll;
use FlexPHP\Bundle\InvoiceBundle\Domain\Payroll\PayrollRepository;
use FlexPHP\Bundle\InvoiceBundle\Domain\Payroll\Request\IndexPayrollRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Payroll\UseCase\IndexPayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;

final class GetPaysheetPayrollsUseCase
{
    private PaysheetRepository $paysheetRepository;

    private PayrollRepository $payrollRepository;

    public function __construct(PaysheetRepository $paysheetRepository, PayrollRepository $payrollRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
        $this->payrollRepository = $payrollRepository;
    }

    /**
     * @return array<Payroll>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->paysheetRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $paysheet = $responsePaysheet->paysheet;

        $payrolls = [];

        if ($paysheet->id()) {
            $requestPayroll = new IndexPayrollRequest([
                'paysheetId' => $paysheet->id(),
            ], 1);

            $useCasePayroll = new IndexPayrollUseCase($this->payrollRepository);

            $responsePayroll = $useCasePayroll->execute($requestPayroll);

            $payrolls = $responsePayroll->payrolls;
        }

        return $payrolls;
    }
}
