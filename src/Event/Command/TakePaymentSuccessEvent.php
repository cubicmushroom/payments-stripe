<?php

namespace CubicMushroom\Payments\Stripe\Event\Command;

use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use CubicMushroom\Payments\Stripe\Event\Events;
use League\Event\Event;

/**
 * Class TakePaymentSuccessEvent
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Event\Command\TakePaymentSuccessEventSpec
 */
class TakePaymentSuccessEvent extends Event implements CommandSucceededEventInterface
{

    /**
     * Sets the event name
     */
    public function __construct()
    {
        parent::__construct(Events::TAKE_PAYMENT_SUCCESS);
    }


}
