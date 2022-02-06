<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetAgreementResponse implements ResponseInterface
{
    public $agreements;

    public function __construct(array $agreements)
    {
        $this->agreements = $agreements;
    }
}
