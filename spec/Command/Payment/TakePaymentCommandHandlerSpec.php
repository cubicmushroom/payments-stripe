<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandHandlerInterface;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Exception\Command\InvalidCommandException;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandler;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
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
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ValueObjects\Web\EmailAddress;

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
    const USER_EMAIL        = 'hello@world.com';
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
     * @var EmailAddress
     */
    protected $userEmail;

    /**
     * @var Payment
     */
    protected $expectedUnpaidPayment;

    /**
     * @var PaymentId
     */
    protected $paymentId;

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
        $this->userEmail                = new EmailAddress(self::USER_EMAIL);
        $this->expectedUnpaidPayment    = new Payment(
            $this->cost, self::TOKEN, self::DESCRIPTION, $this->userEmail
        );
        $this->paymentId                = new PaymentId(self::PAYMENT_ID);
        $this->stripePaymentId          = new StripePaymentId(self::STRIPE_PAYMENT_ID);
        $this->expectedProcessedPayment = (
        new Payment(
            $this->cost, self::TOKEN, self::DESCRIPTION, $this->userEmail
        )
        )->assignId($this->paymentId)
         ->assignGatewayId($this->stripePaymentId)
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
        PaymentRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        // Command
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getCost()->willReturn($this->cost);
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getToken()->willReturn(self::TOKEN);
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getDescription()->willReturn(self::DESCRIPTION);
        /** @noinspection PhpUndefinedMethodInspection */
        $command->getUserEmail()->willReturn($this->userEmail);

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

        /** @see TakePaymentCommandHandler::create() */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->beConstructedThrough('create', [$validator, $emitter, $gateway, $repository, $logger]);
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
        PaymentRepositoryInterface $repository,
        TakePaymentCommand $command
    ) {
        $this->setRepositoryMethodExpectations($repository);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_does_not_handle_other_commands(
        /** @noinspection PhpDocSignatureInspection */
        LoggerInterface $logger
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow(InvalidCommandException::class)->during('handle', [new DummyCommand]);

        /** @noinspection PhpUndefinedMethodInspection */
        $logger->error(
            Argument::containingString(
                TakePaymentCommandHandler::class.' cannot handle commands of type '.DummyCommand::class
            )
        )->shouldHaveBeenCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_validates_the_command(
        /** @noinspection PhpDocSignatureInspection */
        PaymentRepositoryInterface $repository,
        TakePaymentCommand $command,
        ValidatorInterface $validator
    ) {
        $this->setRepositoryMethodExpectations($repository);

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
        PaymentRepositoryInterface $repository,
        TakePaymentCommand $command
    ) {
        $this->setRepositoryMethodExpectations($repository);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_throw_a_payment_failed_exception_if_unable_to_save_unpaid_payment_details(
        /** @noinspection PhpDocSignatureInspection */
        TakePaymentCommand $command,
        PaymentRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpParamsInspection */
        $repository->savePaymentBeforeProcessing(Argument::any())->willThrow(SavePaymentFailedException::class);

        $this->shouldThrow(PaymentFailedException::class)->during('handle', [$command]);

        /** @noinspection PhpUndefinedMethodInspection */
        $commandClass = get_class($command->getWrappedObject());
        /** @noinspection PhpUndefinedMethodInspection */
        $logger->error(
            Argument::containingString(
                "Exception throw while handling {$commandClass} command... Unable to save payment details before " .
                "processing"
            )
        )->shouldBeCalled();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_call_to_confirm_payment_with_stripe(
        /** @noinspection PhpDocSignatureInspection */
        PaymentRepositoryInterface $repository,
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $purchaseRequest
    ) {
        $this->setRepositoryMethodExpectations($repository);

        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(
            [
                'amount'      => self::AMOUNT,
                'currency'    => self::CURRENCY,
                'token'       => self::TOKEN,
                'description' => self::DESCRIPTION,
                'metadata'    => [
                    'paymentId' => self::PAYMENT_ID,
                    'userEmail' => self::USER_EMAIL,
                ],
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
        PaymentRepositoryInterface $repository,
        Gateway $gateway,
        TakePaymentCommand $command
    ) {
        $this->setRepositoryMethodExpectations($repository);
        $this->clearRepositoryMarkAsPaidExpectation($repository);

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
        $this->setRepositoryMethodExpectations($repository);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->handle($command);

        $expectedPayment = new Payment($this->cost, self::TOKEN, self::DESCRIPTION, $this->userEmail);
        $expectedPayment
            ->assignGatewayId(new StripePaymentId(self::STRIPE_PAYMENT_ID))
            ->markAsPaid();
    }


    /**
     * @uses TakePaymentCommandHandler::_handle()
     */
    function it_should_emit_a_success_event_if_all_ok(
        /** @noinspection PhpDocSignatureInspection */
        PaymentRepositoryInterface $repository,
        Gateway $gateway,
        TakePaymentCommand $command,
        PurchaseRequest $purchaseRequest,
        EmitterInterface $emitter
    ) {
        $this->setRepositoryMethodExpectations($repository);

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
        PaymentRepositoryInterface $repository,
        Gateway $gateway,
        TakePaymentCommand $command,
        EmitterInterface $emitter
    ) {
        $this->setRepositoryMethodExpectations($repository);
        $this->clearRepositoryMarkAsPaidExpectation($repository);

        $gatewayException = new PaymentFailedException('Failed to process payment with the Stripe payment gateway');

        /** @noinspection PhpUndefinedMethodInspection */
        $gateway->purchase(Argument::any())->willThrow($gatewayException);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow($gatewayException)->during('handle', [$command]);

        /** @noinspection PhpUndefinedMethodInspection */
        $emitter->emit(Argument::type(TakePaymentFailureEvent::class))->shouldHaveBeenCalled();
    }


    /**
     * Sets the expected calls & responses on PaymentRepositoryInterface stub
     *
     * @param PaymentRepositoryInterface $repository
     */
    protected function setRepositoryMethodExpectations(PaymentRepositoryInterface $repository)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->savePaymentBeforeProcessing($this->expectedUnpaidPayment)
                   ->shouldBeCalled()
                   ->willReturn($this->paymentId);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->markAsPaid($this->expectedProcessedPayment)->shouldBeCalled();
    }


    /**
     * Clears the expected calls on PaymentRepositoryInterface stub markAsPaid() method
     *
     * @param PaymentRepositoryInterface $repository
     */
    protected function clearRepositoryMarkAsPaidExpectation(PaymentRepositoryInterface $repository)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $repository->markAsPaid($this->expectedProcessedPayment)->shouldNotBeCalled();
    }
}


class DummyCommand implements CommandInterface
{
}