<?php

namespace spec\CubicMushroom\Payments\Stripe\Event\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;

class TakePaymentSuccessEventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentSuccessEvent::class);
    }
}
