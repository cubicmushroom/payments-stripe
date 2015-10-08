<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Payments\Stripe\Command\CommandInterface;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class TakePaymentCommandSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand
 */
class TakePaymentCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentCommand::class);
    }


    function it_should_implement_the_command_interface()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(CommandInterface::class);
    }
}
