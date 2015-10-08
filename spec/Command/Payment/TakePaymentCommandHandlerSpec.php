<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Payments\Stripe\Command\CommandHandlerInterface;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TakePaymentCommandHandlerSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler
 */
class TakePaymentCommandHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentCommandHandler::class);
    }


    function it_implements_command_handler_interface()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(CommandHandlerInterface::class);
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_handles_take_payment_commands(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->handler($command);
    }
}
