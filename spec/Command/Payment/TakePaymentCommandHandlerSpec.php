<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandHandlerInterface;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Exception\Command\InvalidCommandException;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterface;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\PaymentFailedException;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;
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
    const AMOUNT            = 999;
    const CURRENCY          = 'GBP';
    const TOKEN             = 'alshclldsacsab';
    const DESCRIPTION       = 'The great unknown is full of conclusion.';
    const PAYMENT_ID        = 52;
    const STRIPE_PAYMENT_ID = 'ch_igc987120ed9230';


    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var Payment
     */
    protected $expectedUnpaidPayment;

    /**
     * @var StripePaymentId
     */
    protected $stripePaymentId;

    /**
     * @var Payment
     */
    protected $expectedProcessedPayment;


    /**
     * Prepare common spec properties
     */
    function __construct()
    {
        $this->currency                 = new Currency(self::CURRENCY);
        $this->cost                     = new Money(self::AMOUNT, $this->currency);
        $this->expectedUnpaidPayment    = new Payment(
            $this->cost,
            self::TOKEN,
            self::DESCRIPTION
        );
        $this->stripePaymentId          = new StripePaymentId(self::STRIPE_PAYMENT_ID);
        $this->expectedProcessedPayment = (
        new Payment(
            $this->cost,
            self::TOKEN,
            self::DESCRIPTION,
            ['paymentId' => self::PAYMENT_ID]
        )
        )->assignGatewayId($this->stripePaymentId)
         ->markAsPaid();
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

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->savePaymentBeforeProcessing($this->expectedUnpaidPayment)->shouldBeCalled();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->markAsPaid($this->expectedProcessedPayment)->shouldBeCalled();

        // Gateway request/response
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())
                ->willReturn($purchaseRequest);
        /** @noinspection PhpUndefinedMethodInspection */
        $purchaseRequest->send()->willReturn($response);
        /** @noinspection PhpUndefinedMethodInspection */
        $response->isSuccessful()->willReturn(true);
        /** @noinspection PhpUndefinedMethodInspection */
        $response->getTransactionReference()->willReturn(self::STRIPE_PAYMENT_ID);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedThrough('create', [$validator, $emitter, $gateway, $repository]);
    }


    function it_is_initializable(PaymentRepositoryInterface $repository)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->savePaymentBeforeProcessing($this->expectedUnpaidPayment)->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $repository->markAsPaid($this->expectedProcessedPayment)->shouldNotBeCalled();

        $this->shouldHaveType(TakePaymentCommandHandler::class);
    }


    function it_implements_command_handler_interface(PaymentRepositoryInterface $repository)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->savePaymentBeforeProcessing($this->expectedUnpaidPayment)->shouldNotBeCalled();
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $repository->markAsPaid($this->expectedProcessedPayment)->shouldNotBeCalled();

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
    function it_creates_an_unpaid_payment_record_first_of_all(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command,
        PaymentRepositoryInterface $repository
    ) {
        $expectedPayment = new Payment($this->cost, self::TOKEN, self::DESCRIPTION);

        // As PHPSpec appears to compare the status of the argument object at the end of the test, rather than at the
        // time of the method call, we need to throw an exception from the gateway to prevent the Payment object being
        // changed before it's used to compare arguments
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())->willThrow(new \Exception('Testing only the initial save!'));

        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow(\Exception::class)->during('handle', [$command]);

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->savePaymentBeforeProcessing($expectedPayment)->shouldBeCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_throw_a_payment_failed_exception_if_unable_to_save_unpaid_payment_details(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command,
        PaymentRepositoryInterface $repository
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpParamsInspection */
        $repository->savePaymentBeforeProcessing(Argument::any())->willThrow(SavePaymentFailedException::class);

        $this->shouldThrow(PaymentFailedException::class)->during('handle', [$command]);
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
    function it_should_throw_a_payment_failed_exception_if_payment_not_processed_by_stripe_ok(
        /** @noinspection PhpDocSignatureInspection */
        Gateway $gateway,
        TakePaymentCommand $command
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())->willThrow(new \Exception);

        $this->shouldThrow(PaymentFailedException::class)->during('handle', [$command]);
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
        $expectedPayment
            ->assignGatewayId(new StripePaymentId(self::RESPONSE_ID))
            ->markAsPaid();


        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $repository->markAsPaid($expectedPayment)->shouldHaveBeenCalled();
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
        $gatewayException = new PaymentFailedException('Failed to process payment with the Stripe payment gateway');

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