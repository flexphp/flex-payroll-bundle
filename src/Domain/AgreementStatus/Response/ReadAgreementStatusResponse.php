<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatus;
use FlexPHP\Messages\ResponseInterface;

final class ReadAgreementStatusResponse implements ResponseInterface
{
    public $agreementStatus;

    public function __construct(AgreementStatus $agreementStatus)
    {
        $this->agreementStatus = $agreementStatus;
    }
}
