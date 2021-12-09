<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementAgreementPeriodResponse implements ResponseInterface
{
    public $agreementPeriods;

    public function __construct(array $agreementPeriods)
    {
        $this->agreementPeriods = $agreementPeriods;
    }
}
