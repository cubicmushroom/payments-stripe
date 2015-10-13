<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Hexagonal\Domain\Generic\ModelId;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PaymentIdSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId
 */
class PaymentIdSpec extends ObjectBehavior
{
    const ID = 5629;

    function let()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedWith(self::ID);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentId::class);
    }

    function it_should_extend_model()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(ModelId::class);
    }


    /**
     * @uses PaymentId::getValue()
     */
    function it_should_return_its_value()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getValue()->shouldReturn(self::ID);
    }
}
