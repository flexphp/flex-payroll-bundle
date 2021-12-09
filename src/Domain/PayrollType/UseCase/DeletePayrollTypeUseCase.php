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
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\DeletePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Response\DeletePayrollTypeResponse;

final class DeletePayrollTypeUseCase
{
    private PayrollTypeRepository $payrollTypeRepository;

    public function __construct(PayrollTypeRepository $payrollTypeRepository)
    {
        $this->payrollTypeRepository = $payrollTypeRepository;
    }

    public function execute(DeletePayrollTypeRequest $request): DeletePayrollTypeResponse
    {
        return new DeletePayrollTypeResponse($this->payrollTypeRepository->remove($request));
    }
}
