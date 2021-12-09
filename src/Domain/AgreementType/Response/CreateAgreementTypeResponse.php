<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementType;
use FlexPHP\Messages\ResponseInterface;

final class CreateAgreementTypeResponse implements ResponseInterface
{
    public $agreementType;

    public function __construct(AgreementType $agreementType)
    {
        $this->agreementType = $agreementType;
    }
}
