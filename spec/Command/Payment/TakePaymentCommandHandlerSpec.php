<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandHandlerInterface;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Exception\Command\InvalidCommandException;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use League\Event\EmitterInterface;
use Money\Currency;
use Money\Money;
use Omnipay\Stripe\Gateway;
use Omnipay\Stripe\Message\PurchaseRequest;
use PhpSpec\Exception\Example\PendingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class TakePaymentCommandHandlerSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler
 */
class TakePaymentCommandHandlerSpec extends ObjectBehavior
{
    const AMOUNT   = 999;
    const CURRENCY = 'GBP';
    const TOKEN    = 'alshclldsacsab';


    /**
     * @uses TakePaymentCommandHandler::__construct()
     */
    function let(
        /** @noinspection PhpDocSignatureInspection */
        ValidatorInterface $validator,
        EmitterInterface $emitter,
        TakePaymentCommand $command,
        Gateway $gateway
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getCost()->willReturn(new Money(self::AMOUNT, new Currency(self::CURRENCY)));
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getToken()->willReturn(self::TOKEN);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedThrough('create', [$validator, $emitter, $gateway]);
    }


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
        TakePaymentCommand $command
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_does_not_handle_other_commands()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow(InvalidCommandException::class)->during('handle', [new DummyCommand]);
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_validates_the_command(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command,
        ValidatorInterface $validator
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        /** @noinspection PhpUndefinedMethodInspection */
        $validator->validate($command)->shouldHaveBeenCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_should_call_to_confirm_payment_with_stripe(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(['amount' => self::AMOUNT, 'currency' => self::CURRENCY, 'token' => self::TOKEN])
                ->shouldBeCalled();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    function it_should_store_a_successful_payment(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $response
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())
                ->willReturn($response);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        throw new PendingException('Add check for repository call, once repository spec complete');
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_should_emit_a_success_event_if_all_ok(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $response,
        EmitterInterface $emitter
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())
                ->willReturn($response)
                ->shouldBeCalled();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        /** @noinspection PhpUndefinedMethodInspection */
        $emitter->emit(Argument::type(TakePaymentSuccessEvent::class))->shouldHaveBeenCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::handle()
     */
    function it_should_emit_a_failure_event_if_not_ok(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        EmitterInterface $emitter
    ) {
        $gatewayException = new \Exception('payment failed');

        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())->willThrow($gatewayException);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow($gatewayException)->during('handle', [$command]);

        /** @noinspection PhpUndefinedMethodInspection */
        $emitter->emit(Argument::type(TakePaymentFailureEvent::class))->shouldHaveBeenCalled();
    }
}


class DummyCommand implements CommandInterface
{
}