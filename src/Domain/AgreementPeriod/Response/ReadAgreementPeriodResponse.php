<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriod;
use FlexPHP\Messages\ResponseInterface;

final class ReadAgreementPeriodResponse implements ResponseInterface
{
    public $agreementPeriod;

    public function __construct(AgreementPeriod $agreementPeriod)
    {
        $this->agreementPeriod = $agreementPeriod;
    }
}
