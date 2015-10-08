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
    const TOKEN       = 'ugcashdcial';
    const DESCRIPTION = 'Try chopping seaweed tart garnished with honey.';

    protected $metaData = [
        1   => 'abc',
        'a' => 'xyz',
    ];


    /**
     * @uses Payment::__constuct()
     */
    public function let(Money $cost)
    {
        // @todo - Make $token into a value object
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection SpellCheckingInspection */
        $this->beConstructedWith($cost, self::TOKEN, self::DESCRIPTION, $this->metaData);
    }


    /**
     * @uses Payment::__construct()
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }


    /**
     * @uses Payment::getAmount()
     * @uses Payment::getCurrency()
     * @uses Payment::getCost()
     */
    function it_returns_the_amount_and_currency(
        /** @noinspection PhpDocSignatureInspection */
        Money $cost,
        Currency $currency
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $cost->getAmount()->willReturn(999);
        /** @noinspection PhpUndefinedMethodInspection */
        $cost->getCurrency()->willReturn($currency);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAmount()->shouldReturn(999);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCurrency()->shouldReturn($currency);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCost()->shouldReturn($cost);
    }


    /**
     * @uses Payment::getToken()
     */
    function it_returns_the_token()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getToken()->shouldReturn(self::TOKEN);
    }


    /**
     * @uses Payment::getDescription()
     */
    function it_returns_the_description()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getDescription()->shouldReturn(self::DESCRIPTION);
    }


    /**
     * @uses Payment::getMetaData()
     */
    function it_returns_metadata()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getMetaData()->shouldReturn($this->metaData);
    }
}
