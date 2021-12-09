<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementEmployeeResponse implements ResponseInterface
{
    public $employees;

    public function __construct(array $employees)
    {
        $this->employees = $employees;
    }
}
