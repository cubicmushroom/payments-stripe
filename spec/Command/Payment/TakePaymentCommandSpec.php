<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use Money\Currency;
use Money\Money;
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


    /**
     * @uses TakePaymentCommand::cost()
     */
    function it_should_be_initialise_through_static_builder()
    {
        $cost = new Money(999, new Currency('GBP')  );
        /** @noinspection SpellCheckingInspection */
        $token = 't7398ryhslddvd';

        $this->beConstructedThrough('create', [$cost, $token]);
        $this->shouldHaveType(TakePaymentCommand::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCost()->shouldReturn($cost);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getToken()->shouldReturn($token);
    }
}
