<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatusRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\DeletePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Response\DeletePayrollStatusResponse;

final class DeletePayrollStatusUseCase
{
    private PayrollStatusRepository $payrollStatusRepository;

    public function __construct(PayrollStatusRepository $payrollStatusRepository)
    {
        $this->payrollStatusRepository = $payrollStatusRepository;
    }

    public function execute(DeletePayrollStatusRequest $request): DeletePayrollStatusResponse
    {
        return new DeletePayrollStatusResponse($this->payrollStatusRepository->remove($request));
    }
}
