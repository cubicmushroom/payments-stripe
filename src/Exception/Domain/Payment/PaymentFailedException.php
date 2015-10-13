<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/10/15
 * Time: 11:17
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

/**
 * Exception thrown by handler if payment fails.  Should contain the previous exception as to why the payment failed
 *
 * @package CubicMushroom\Payments\Stripe
 */
class PaymentFailedException extends AbstractPaymentException
{
}