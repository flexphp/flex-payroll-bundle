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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetail;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\IndexPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\IndexPaysheetDetailUseCase;

final class GetPaysheetPayrollDetailsUseCase
{
    private PaysheetRepository $orderRepository;

    private PaysheetDetailRepository $orderDetailRepository;

    public function __construct(PaysheetRepository $orderRepository, PaysheetDetailRepository $orderDetailRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
    }

    /**
     * @return array<PaysheetDetail>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->orderRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $order = $responsePaysheet->order;

        $orderDetails = [];

        if ($order->id()) {
            $requestPaysheetDetails = new IndexPaysheetDetailRequest([
                'orderId' => $order->id(),
            ], 1);

            $useCasePaysheetDetails = new IndexPaysheetDetailUseCase($this->orderDetailRepository);

            $responsePaysheetDetails = $useCasePaysheetDetails->execute($requestPaysheetDetails);

            $orderDetails = $responsePaysheetDetails->orderDetails;
        }

        return $orderDetails;
    }
}
