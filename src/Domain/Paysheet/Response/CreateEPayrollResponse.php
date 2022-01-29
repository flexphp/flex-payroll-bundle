<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class CreateEPayrollResponse implements ResponseInterface
{
    public $status;

    public $message;

    public $filename;

    public $content;

    public function __construct(string $status, string $message, string $filename = null, string $content = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->filename = $filename;
        $this->content = $content;
    }
}
