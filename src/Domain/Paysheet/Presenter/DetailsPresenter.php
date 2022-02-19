<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Presenter;

class DetailsPresenter
{
    private array $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function basic(): BasicPresenter
    {
        return new BasicPresenter($this->details['accrued']['basic'][0]);
    }

    public function transport(): TransportPresenter
    {
        return new TransportPresenter($this->details['accrued']['transport'][0]);
    }

    public function vacation(): VacationPresenter
    {
        return new VacationPresenter($this->details['accrued']['vacation'][0]);
    }

    public function bonus(): BonusPresenter
    {
        return new BonusPresenter($this->details['accrued']['bonus'][0]);
    }

    public function cessation(): CessationPresenter
    {
        return new CessationPresenter($this->details['accrued']['cessation'][0]);
    }

    public function supports(): SupportPresenter
    {
        return new SupportPresenter($this->details['accrued']['support']);
    }

    public function endowment(): EndowmentPresenter
    {
        return new EndowmentPresenter($this->details['accrued']['endowment'][0]);
    }

    public function health(): HealthPresenter
    {
        return new HealthPresenter($this->details['deduction']['health'][0]);
    }

    public function pension(): PensionPresenter
    {
        return new PensionPresenter($this->details['deduction']['pension'][0]);
    }
}
