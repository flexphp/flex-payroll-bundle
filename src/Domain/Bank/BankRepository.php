<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank;

use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\CreateBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\DeleteBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\IndexBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\ReadBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\UpdateBankRequest;

final class BankRepository
{
    private BankGateway $gateway;

    public function __construct(BankGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<Bank>
     */
    public function findBy(IndexBankRequest $request): array
    {
        return \array_map(function (array $bank) {
            return (new BankFactory())->make($bank);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateBankRequest $request): Bank
    {
        $bank = (new BankFactory())->make($request);

        $bank->setId($this->gateway->push($bank));

        return $bank;
    }

    public function getById(ReadBankRequest $request): Bank
    {
        $factory = new BankFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateBankRequest $request): Bank
    {
        $bank = (new BankFactory())->make($request);

        $this->gateway->shift($bank);

        return $bank;
    }

    public function remove(DeleteBankRequest $request): Bank
    {
        $factory = new BankFactory();
        $data = $this->gateway->get($factory->make($request));

        $bank = $factory->make($data);

        $this->gateway->pop($bank);

        return $bank;
    }
}
