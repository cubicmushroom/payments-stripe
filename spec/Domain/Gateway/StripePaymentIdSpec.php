<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Gateway;

use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StripePaymentIdSpec extends ObjectBehavior
{
    const ID = 'ch_kdacljc99cyaie';


    /**
     * @uses StripePaymentId::__construct()
     */
    function let()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedWith(self::ID);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StripePaymentId::class);
    }


    /**
     * @uses StripePaymentId::getValue()
     */
    function it_returns_the_set_value()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getValue()->shouldReturn(self::ID);
    }


    /**
     * @uses StripePaymentId::getValue()
     */
    function it_convert_to_string()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->__toString()->shouldReturn(self::ID);
    }
}
