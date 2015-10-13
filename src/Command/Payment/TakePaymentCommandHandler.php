<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal;
use CubicMushroom\Hexagonal\Command\AbstractCommandHandler;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterface;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\PaymentFailedException;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;
use League\Event\EmitterInterface;
use Omnipay\Stripe\Gateway;
use Omnipay\Stripe\Message\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class TakePaymentCommandHandler
 *
 * Command: TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @todo    - Add Logging to all exception throws
 */
class TakePaymentCommandHandler extends AbstractCommandHandler
{

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
     * @throws PaymentFailedException
     * @throws SavePaymentFailedException
     */
    protected function _handle(CommandInterface $command)
    {
        $this->validator->validate($command);

        $payment = $this->convertCommandToPayment($command);

        try {
            // We clone the payment object here, so PHPSpec can test it, until the following issue is resolved…
            // https://github.com/phpspec/phpspec/issues/789
            $paymentId = $this->repository->savePaymentBeforeProcessing(clone $payment);
            $payment->assignId($paymentId);
        } catch (\Exception $exception) {
            throw PaymentFailedException::createWithPayment(
                $payment,
                'Unable to save payment details before processing',
                0,
                $exception
            );
        }

        try {
            $purchaseRequest  = $this->gateway->purchase($payment->getGatewayPurchaseArray());
            $purchaseResponse = $purchaseRequest->send();
            if (!$purchaseResponse->isSuccessful()) {
                throw PaymentFailedException::createWithPayment($payment, $purchaseResponse->getMessage());
            }
        } catch (\Exception $gatewayException) {
            throw PaymentFailedException::createWithPayment(
                $payment,
                'Failed to process payment with the Stripe payment gateway',
                0,
                $gatewayException
            );
        }

        $this->updatePaymentWithStripeCharge($payment, $purchaseResponse);
    }


    /**
     * @param TakePaymentCommand $command
     *
     * @return Payment
     */
    protected function convertCommandToPayment(TakePaymentCommand $command)
    {
        $payment = new Payment($command->getCost(), $command->getToken(), $command->getDescription());

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
        $payment
            ->assignGatewayId(new StripePaymentId($purchaseResponse->getTransactionReference()))
            ->markAsPaid();

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
        return new TakePaymentSuccessEvent($command);
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
