<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Bank;
use FlexPHP\Messages\ResponseInterface;

final class DeleteBankResponse implements ResponseInterface
{
    public $bank;

    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }
}
