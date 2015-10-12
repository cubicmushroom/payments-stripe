<?php

namespace spec\CubicMushroom\Payments\Stripe\Event\Command;

use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TakePaymentFailureEventSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent
 */
class TakePaymentFailureEventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentFailureEvent::class);
    }


    function it_should_implement_the_command_failed_event_interface()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(CommandFailedEventInterface::class);
    }
}
