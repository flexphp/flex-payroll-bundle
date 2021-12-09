<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\IndexPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Response\IndexPayrollTypeResponse;

final class IndexPayrollTypeUseCase
{
    private PayrollTypeRepository $payrollTypeRepository;

    public function __construct(PayrollTypeRepository $payrollTypeRepository)
    {
        $this->payrollTypeRepository = $payrollTypeRepository;
    }

    public function execute(IndexPayrollTypeRequest $request): IndexPayrollTypeResponse
    {
        $payrollTypes = $this->payrollTypeRepository->findBy($request);

        return new IndexPayrollTypeResponse($payrollTypes);
    }
}
