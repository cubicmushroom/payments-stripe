<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 08/10/15
 * Time: 23:26
 */

namespace CubicMushroom\Payments\Stripe\Command;

/**
 * Class CommandHandlerInterface
 *
 * @package CubicMushroom\Payments\Stripe
 */
interface CommandHandlerInterface
{
    /**
     * Processes the associated command
     *
     * @param CommandInterface $command
     *
     * @return void
     */
    public function handle(CommandInterface $command);
}