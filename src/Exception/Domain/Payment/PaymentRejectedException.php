<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 19/10/15
 * Time: 05:28
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

/**
 * Exception thrown when a payment is rejected by the gateway
 *
 * @package CubicMushroom\Payments
 */
class PaymentRejectedException extends AbstractPaymentException
{
}