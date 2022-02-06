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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\ReadAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementRepository;

final class GetPaysheetAgreementUseCase
{
    private PaysheetRepository $paysheetRepository;

    private AgreementRepository $agreementRepository;

    public function __construct(PaysheetRepository $paysheetRepository, AgreementRepository $agreementRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
        $this->agreementRepository = $agreementRepository;
    }

    public function execute(ReadPaysheetRequest $request): Agreement
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->paysheetRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $paysheet = $responsePaysheet->paysheet;

        $agreement = new Agreement();

        if ($paysheet->agreementId()) {
            $requestAgreement = new ReadAgreementRequest($paysheet->agreementId());

            $useCaseAgreement = new ReadAgreementUseCase($this->agreementRepository);

            $responseAgreement = $useCaseAgreement->execute($requestAgreement);

            $agreement = $responseAgreement->agreement;
        }

        return $agreement;
    }
}
