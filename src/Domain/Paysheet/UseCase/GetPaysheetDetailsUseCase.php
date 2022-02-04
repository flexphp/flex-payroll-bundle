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

final class GetPaysheetDetailsUseCase
{
    private PaysheetRepository $paysheetRepository;

    private PaysheetDetailRepository $paysheetDetailRepository;

    public function __construct(PaysheetRepository $paysheetRepository, PaysheetDetailRepository $paysheetDetailRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
        $this->paysheetDetailRepository = $paysheetDetailRepository;
    }

    /**
     * @return array<PaysheetDetail>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->paysheetRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $paysheet = $responsePaysheet->paysheet;

        $paysheetDetails = [];

        if ($paysheet->id()) {
            $requestPaysheetDetails = new IndexPaysheetDetailRequest([
                'paysheetId' => $paysheet->id(),
            ], 1);

            $useCasePaysheetDetails = new IndexPaysheetDetailUseCase($this->paysheetDetailRepository);

            $responsePaysheetDetails = $useCasePaysheetDetails->execute($requestPaysheetDetails);

            $paysheetDetails = $responsePaysheetDetails->paysheetDetails;
        }

        return $paysheetDetails;
    }
}
