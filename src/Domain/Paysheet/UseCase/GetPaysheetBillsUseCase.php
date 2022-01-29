<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Bill;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\BillRepository;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\IndexBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\IndexBillUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;

final class GetPaysheetBillsUseCase
{
    private PaysheetRepository $orderRepository;

    private BillRepository $billRepository;

    public function __construct(PaysheetRepository $orderRepository, BillRepository $billRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->billRepository = $billRepository;
    }

    /**
     * @return array<Bill>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->orderRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $order = $responsePaysheet->order;

        $bills = [];

        if ($order->id()) {
            $requestBill = new IndexBillRequest([
                'orderId' => $order->id(),
            ], 1);

            $useCaseBill = new IndexBillUseCase($this->billRepository);

            $responseBill = $useCaseBill->execute($requestBill);

            $bills = $responseBill->bills;
        }

        return $bills;
    }
}
