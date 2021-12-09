<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request;

use FlexPHP\Messages\RequestInterface;

final class FindBankUserRequest implements RequestInterface
{
    public $term;

    public $_page;

    public $_limit;

    public function __construct(array $data)
    {
        $this->term = $data['term'] ?? '';
        $this->_page = $data['page'] ?? 1;
        $this->_limit = $data['limit'] ?? 20;
    }
}
