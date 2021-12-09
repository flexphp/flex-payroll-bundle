<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementAgreementStatusResponse implements ResponseInterface
{
    public $agreementStatus;

    public function __construct(array $agreementStatus)
    {
        $this->agreementStatus = $agreementStatus;
    }
}
