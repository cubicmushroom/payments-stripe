<?php

namespace spec\CubicMushroom\Payments\Stripe\Event\Command;

use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;

/**
 * Class TakePaymentSuccessEventSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent
 */
class TakePaymentSuccessEventSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentSuccessEvent::class);
    }


    function it_must_implement_command_success_event_interface()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(CommandSucceededEventInterface::class);
    }
}
