<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexBankResponse implements ResponseInterface
{
    public $banks;

    public function __construct(array $banks)
    {
        $this->banks = $banks;
    }
}
