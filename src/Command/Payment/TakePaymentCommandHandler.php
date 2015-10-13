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
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\GatewayPaymentException;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;
use League\Event\EmitterInterface;
use Omnipay\Stripe\Gateway;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class TakePaymentCommandHandler
 *
 * Command: TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
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


    /** @noinspection PhpDocSignatureInspection */
    /**
     * Processes the associated command
     *
     * @param CommandInterface|TakePaymentCommand $command
     *
     * @throws GatewayPaymentException
     * @throws SavePaymentFailedException
     */
    protected function _handle(CommandInterface $command)
    {
        $this->validator->validate($command);

        $payment = $this->convertCommandToPayment($command);

        try {
            $purchaseRequest  = $this->gateway->purchase($payment->getGatewayPurchaseArray());
            $purchaseResponse = $purchaseRequest->send();
            if (!$purchaseResponse->isSuccessful()) {
                throw GatewayPaymentException::createWithPayment($payment, $purchaseResponse->getMessage());
            }
        } catch (\Exception $gatewayException) {
            throw GatewayPaymentException::createWithPayment(
                $payment,
                'Failed to process payment with the Stripe payment gateway',
                0,
                $gatewayException
            );
        }

        try {
            $payment->assignGatewayId(new StripePaymentId($purchaseResponse->getTransactionReference()));
            $this->repository->saveSuccessfulPayment($payment);
        } catch (SavePaymentFailedException $savePaymentFailedException) {
            throw $savePaymentFailedException;
        }
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
