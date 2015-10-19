<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/10/15
 * Time: 11:17
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\PublicSafeMessageInterface;

/**
 * Exception thrown by handler if payment fails.
 *
 * Should contain the previous exception as to why the payment failed
 *
 * Should also contain a public safe message
 *
 * @package CubicMushroom\Payments\Stripe
 */
class PaymentFailedException extends AbstractPaymentException implements PublicSafeMessageInterface
{
    /**
     * Returns a message to display to the website user
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return 'Your payment failed.  Please try again';
    }
}