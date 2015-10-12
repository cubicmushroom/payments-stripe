<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaymentRepositoryInterfaceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRepositoryInterface::class);
    }
}
