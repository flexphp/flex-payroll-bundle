<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType;

use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\CreateAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\DeleteAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\IndexAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\ReadAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\UpdateAccountTypeRequest;

final class AccountTypeRepository
{
    private AccountTypeGateway $gateway;

    public function __construct(AccountTypeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<AccountType>
     */
    public function findBy(IndexAccountTypeRequest $request): array
    {
        return \array_map(function (array $accountType) {
            return (new AccountTypeFactory())->make($accountType);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateAccountTypeRequest $request): AccountType
    {
        $accountType = (new AccountTypeFactory())->make($request);

        $accountType->setId($this->gateway->push($accountType));

        return $accountType;
    }

    public function getById(ReadAccountTypeRequest $request): AccountType
    {
        $factory = new AccountTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateAccountTypeRequest $request): AccountType
    {
        $accountType = (new AccountTypeFactory())->make($request);

        $this->gateway->shift($accountType);

        return $accountType;
    }

    public function remove(DeleteAccountTypeRequest $request): AccountType
    {
        $factory = new AccountTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        $accountType = $factory->make($data);

        $this->gateway->pop($accountType);

        return $accountType;
    }
}
