<?php

namespace CubicMushroom\Payments\Stripe\Event\Command;

use CubicMushroom\Hexagonal\Event\CommandSucceededEventInterface;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
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
     * @var PaymentId
     */
    protected $paymentId;


    // -----------------------------------------------------------------------------------------------------------------
    // Constructor methods
    // -----------------------------------------------------------------------------------------------------------------


    public static function create(PaymentId $paymentId)
    {
        /** @var TakePaymentSuccessEvent $event */
        $event = new static();

        $event->paymentId = $paymentId;

        return $event;
    }


    /**
     * Sets the event name
     */
    public function __construct()
    {
        parent::__construct(Events::TAKE_PAYMENT_SUCCESS);
    }


    /**
     * @return PaymentId
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }


}
