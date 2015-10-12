<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal;
use CubicMushroom\Hexagonal\Command\AbstractCommandHandler;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEvent;
use CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEvent;
use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\GatewayPaymentException;
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
     * @param ValidatorInterface $validator
     * @param EmitterInterface   $emitter
     * @param Gateway            $gateway
     *
     * @return static
     */
    public static function create(ValidatorInterface $validator, EmitterInterface $emitter, Gateway $gateway)
    {
        /** @var self $handler */
        $handler = parent::createBasic($validator, $emitter);

        $handler->gateway = $gateway;

        return $handler;
    }

    /**
     * @var Gateway
     */
    protected $gateway;


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
     * @param TakePaymentCommand $command
     *
     * @return void
     */
    protected function _handle(CommandInterface $command)
    {
        $this->validator->validate($command);

        $payment = $this->convertCommandToPayment($command);

        try {
            $this->gateway->purchase($payment->getGatewayPurchaseArray());
        } catch (\Exception $gatewayException) {
            throw GatewayPaymentException::createWithPayment($payment, 'The Stripe Payment gateway failed to process payment', 0, $gatewayException);
        }
    }


    /**
     * @param TakePaymentCommand $command
     *
     * @return Payment
     */
    protected function convertCommandToPayment(TakePaymentCommand $command)
    {
        // @todo - Add support for payment description
        $payment = new Payment($command->getCost(), $command->getToken(), '');

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
