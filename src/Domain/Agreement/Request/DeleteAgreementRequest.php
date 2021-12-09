<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request;

use FlexPHP\Messages\RequestInterface;

final class DeleteAgreementRequest implements RequestInterface
{
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
