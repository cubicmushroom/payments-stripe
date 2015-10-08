<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use Money\Currency;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class PaymentSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Domain\Payment\Payment
 */
class PaymentSpec extends ObjectBehavior
{
    public function let(Money $cost)
    {
        // @todo - Make $token into a value object
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection SpellCheckingInspection */
        $this->beConstructedWith($cost, $token = 'ugcashdcial');
    }


    /**
     * @uses Payment::__construct()
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }


    /**
     * @uses Payment::__construct()
     * @uses Payment::getAmount()
     * @uses Payment::getCurrency()
     * @uses Payment::getCost()
     */
    function it_returns_the_amount_and_currency(
        /** @noinspection PhpDocSignatureInspection */
        Money $cost, Currency $currency)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $cost->getAmount()->willReturn(999);
        /** @noinspection PhpUndefinedMethodInspection */
        $cost->getCurrency()->willReturn($currency);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection SpellCheckingInspection */
        $this->beConstructedWith($cost, 'hiahicdscd');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAmount()->shouldReturn(999);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCurrency()->shouldReturn($currency);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCost()->shouldReturn($cost);
    }


}
