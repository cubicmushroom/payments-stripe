<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal;
use CubicMushroom\Hexagonal\Command\AbstractCommandHandler;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterface;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\PaymentFailedException;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\PaymentNotAuthorisedException;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;
use League\Event\EmitterInterface;
use Omnipay\Stripe\Gateway;
use Omnipay\Stripe\Message\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class TakePaymentCommandHandler
 *
 * Command: TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandHandlerSpec
 *
 * @todo    - Add Logging to all exception throws
 */
class TakePaymentCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PaymentId
     */
    protected $paymentId;


    /**
     * @param ValidatorInterface         $validator
     * @param EmitterInterface           $emitter
     * @param Gateway                    $gateway
     * @param PaymentRepositoryInterface $repository
     *
     * @return static
     */
    public static function create(
        ValidatorInterface $validator,
        EmitterInterface $emitter,
        Gateway $gateway,
        PaymentRepositoryInterface $repository
    ) {
        /** @var self $handler */
        $handler = parent::createBasic($validator, $emitter);

        $handler->gateway    = $gateway;
        $handler->repository = $repository;

        return $handler;
    }


    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var PaymentRepositoryInterface
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * Please use `create()` method to construct
     */
    protected function __construct()
    {
    }


    /**
     * Processes the associated command
     *
     * @param CommandInterface|TakePaymentCommand $command
     *
     * @throws PaymentFailedException     if unable to create new payment record, or the gateway authorisation failed
     *                                    form any reason
     * @throws SavePaymentFailedException if the payment succeeds, but something goes wrong when updating the payment
     *                                    record
     */
    protected function _handle(CommandInterface $command)
    {
        $this->paymentId = null;

        $payment = $this->convertCommandToPayment($command);

        try {
            // We clone the payment object here, so PHPSpec can test it, until the following issue is resolved…
            // https://github.com/phpspec/phpspec/issues/789
            $this->paymentId = $this->repository->savePaymentBeforeProcessing($payment);
            $payment->assignId($this->paymentId);

            $purchaseRequest  = $this->gateway->purchase($payment->getGatewayPurchaseArray());
            $purchaseResponse = $purchaseRequest->send();
            if (!$purchaseResponse->isSuccessful()) {
                // @todo Add saving of failed details to payment record
                throw PaymentNotAuthorisedException::createWithPayment($payment, $purchaseResponse->getMessage());
            }
        } catch (PaymentFailedException $exception) {
            // These exceptions will be publicly safe, so just throw themhttps:/https://github.com/phpspec/phpspec/issues/789https://github.com/phpspec/phpspec/issues/789https://github.com/phpspec/phpspec/issues/789https://github.com/phpspec/phpspec/issues/789/github.com/phpspec/phpspec/issues/789
            throw $exception;
        } catch (\Exception $exception) {
            throw PaymentFailedException::createWithPayment(
                $payment,
                sprintf('An unrecognised exception (%s) has been thrown', get_class($exception)),
                0,
                $exception
            );
        }

        try {
            $this->updatePaymentWithStripeCharge($payment, $purchaseResponse);
        } catch (\Exception $exception) {
            // Wrap in SavePaymentFailedException if need be to provide public exception
            if ($exception instanceof SavePaymentFailedException) {
                throw $exception;
            }
            throw SavePaymentFailedException::createWithPayment(
                $payment,
                sprintf('An unrecognised exception (%s) has been thrown', get_class($exception)),
                0,
                $exception
            );
        }
    }


    /**
     * @param TakePaymentCommand $command
     *
     * @return Payment
     */
    protected function convertCommandToPayment(TakePaymentCommand $command)
    {
        $payment = Payment::createUnpaid(
            $command->getCost(),
            $command->getToken(),
            $command->getDescription(),
            $command->getUserEmail(),
            $command->getMetaData()
        );

        return $payment;
    }


    /**
     * @param Payment  $payment
     * @param Response $purchaseResponse
     *
     * @throws SavePaymentFailedException
     */
    protected function updatePaymentWithStripeCharge(Payment $payment, Response $purchaseResponse)
    {
        $payment->hasBeenPaidWithGatewayTransaction(new StripePaymentId($purchaseResponse->getTransactionReference()));

        try {
            $this->repository->markAsPaid($payment);
        } catch (SavePaymentFailedException $savePaymentFailedException) {
            throw $savePaymentFailedException;
        }
    }


    /**
     * Should return the class name of the class this handler handles
     *
     * @return string
     */
    protected function getCommandClass()
    {
        return TakePaymentCommand::class;
    }


    /**
     * @param CommandInterface $command
     *
     * @return CommandSucceededEventInterface
     */
    protected function getSuccessEvent(CommandInterface $command)
    {
        return TakePaymentSuccessEvent::create($this->paymentId);
    }


    /**
     * @param \Exception $exception
     *
     * @return CommandFailedEventInterface
     */
    protected function getFailureEvent(\Exception $exception)
    {
        return new TakePaymentFailureEvent($exception);
    }
}
