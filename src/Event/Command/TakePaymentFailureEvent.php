<?php

namespace CubicMushroom\Payments\Stripe\Event\Command;

use CubicMushroom\Hexagonal\Event\CommandFailedEventInterface;
use CubicMushroom\Payments\Stripe\Event\Events;
use League\Event\Event;

/**
 * Class TakePaymentFailureEvent
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     /spec\CubicMushroom\Payments\Stripe\Event\Command\TakePaymentFailureEventSpec
 */
class TakePaymentFailureEvent extends Event implements CommandFailedEventInterface
{
    /**
     * Sets the event name
     */
    public function __construct()
    {
        parent::__construct(Events::TAKE_PAYMENT_FAILURE);
    }


}
