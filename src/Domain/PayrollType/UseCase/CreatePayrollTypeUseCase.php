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
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\CreatePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Response\CreatePayrollTypeResponse;

final class CreatePayrollTypeUseCase
{
    private PayrollTypeRepository $payrollTypeRepository;

    public function __construct(PayrollTypeRepository $payrollTypeRepository)
    {
        $this->payrollTypeRepository = $payrollTypeRepository;
    }

    public function execute(CreatePayrollTypeRequest $request): CreatePayrollTypeResponse
    {
        return new CreatePayrollTypeResponse($this->payrollTypeRepository->add($request));
    }
}
