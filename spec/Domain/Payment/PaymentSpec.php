<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class PaymentSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \CubicMushroom\Payments\Stripe\Domain\Payment\Payment
 */
class PaymentSpec extends ObjectBehavior
{
    /**
     * @uses Payment
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }


}
