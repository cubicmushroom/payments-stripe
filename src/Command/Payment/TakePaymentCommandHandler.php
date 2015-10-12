<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal;
use CubicMushroom\Hexagonal\Command\AbstractCommandHandler;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use Omnipay\Stripe\Gateway;


/**
 * Class TakePaymentCommandHandler
 *
 * Command: TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
 */
class TakePaymentCommandHandler extends AbstractCommandHandler
{
    use Hexagonal\Command\CommandValidatorTrait;
    use Hexagonal\Command\EventHelperTrait;

    /**
     * @var Gateway
     */
    protected $gateway;


    /**
     * TakePaymentCommandHandler constructor.
     *
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
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

        $amount   = $command->getCost()->getAmount();
        $currency = $command->getCost()->getCurrency();
        $token    = $command->getToken();
        $this->gateway->purchase(compact('amount', 'currency', 'token'));
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
        // TODO: Implement getSuccessEvent() method.
    }


    /**
     * @param \Exception $exception
     *
     * @return CommandFailedEventInterface
     */
    protected function getFailureEvent(\Exception $exception)
    {
        // TODO: Implement getFailureEvent() method.
    }
}
