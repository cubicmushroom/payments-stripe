<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Payments\Stripe\Command\CommandHandlerInterface;
use CubicMushroom\Payments\Stripe\Command\CommandInterface;


/**
 * Class TakePaymentCommandHandler
 *
 * Command: TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
 */
class TakePaymentCommandHandler implements CommandHandlerInterface
{
    /**
     * Processes the associated command
     *
     * @param CommandInterface $command
     *
     * @return void
     */
    public function handle(CommandInterface $command)
    {
    }

    public function handler($argument1)
    {
    }
}
