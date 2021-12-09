<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementAgreementTypeResponse implements ResponseInterface
{
    public $agreementTypes;

    public function __construct(array $agreementTypes)
    {
        $this->agreementTypes = $agreementTypes;
    }
}
