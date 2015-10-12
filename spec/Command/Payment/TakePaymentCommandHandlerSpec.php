<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandHandlerInterface;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Exception\Command\InvalidCommandException;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterface;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\GatewayPaymentException;
use League\Event\EmitterInterface;
use Money\Currency;
use Money\Money;
use Omnipay\Stripe\Gateway;
use Omnipay\Stripe\Message\PurchaseRequest;
use Omnipay\Stripe\Message\Response;
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
    const AMOUNT      = 999;
    const CURRENCY    = 'GBP';
    const TOKEN       = 'alshclldsacsab';
    const DESCRIPTION = 'The great unknown is full of conclusion.';


    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var Currency
     */
    protected $currency;


    /**
     * Prepare common spec properties
     */
    function __construct()
    {
        $this->currency = new Currency(self::CURRENCY);
        $this->cost     = new Money(self::AMOUNT, $this->currency);
    }


    /**
     * @uses TakePaymentCommandHandler::__construct()
     */
    function let(
        /** @noinspection PhpDocSignatureInspection */
        ValidatorInterface $validator,
        EmitterInterface $emitter,
        TakePaymentCommand $command,
        Gateway $gateway,
        PurchaseRequest $purchaseRequest,
        Response $response,
        PaymentRepositoryInterface $repository
    ) {
        // Command
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getCost()->willReturn($this->cost);
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getToken()->willReturn(self::TOKEN);
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getDescription()->willReturn(self::DESCRIPTION);

        // Gateway request/response
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())
                ->willReturn($purchaseRequest);
        /** @noinspection PhpUndefinedMethodInspection */
        $purchaseRequest->send()->willReturn($response);
        /** @noinspection PhpUndefinedMethodInspection */
        $response->isSuccessful()->willReturn(true);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedThrough('create', [$validator, $emitter, $gateway, $repository]);
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
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_handles_take_payment_commands(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command
    ) {

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_does_not_handle_other_commands()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow(InvalidCommandException::class)->during('handle', [new DummyCommand]);
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
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
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_call_to_confirm_payment_with_stripe(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $purchaseRequest
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(
            [
                'amount'      => self::AMOUNT,
                'currency'    => self::CURRENCY,
                'token'       => self::TOKEN,
                'description' => self::DESCRIPTION,
            ]
        )
                ->willReturn($purchaseRequest)
                ->shouldBeCalled();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_store_a_successful_payment(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command,
        PaymentRepositoryInterface $repository
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        $expectedPayment = new Payment($this->cost, self::TOKEN, self::DESCRIPTION);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $repository->saveSuccessfulPayment($expectedPayment)->shouldHaveBeenCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_emit_a_success_event_if_all_ok(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $purchaseRequest,
        EmitterInterface $emitter
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())
                ->willReturn($purchaseRequest)
                ->shouldBeCalled();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        /** @noinspection PhpUndefinedMethodInspection */
        $emitter->emit(Argument::type(TakePaymentSuccessEvent::class))->shouldHaveBeenCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_emit_a_failure_event_if_not_ok(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        EmitterInterface $emitter
    ) {
        $gatewayException = new GatewayPaymentException('Failed to process payment with the Stripe payment gateway');

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