<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/10/15
 * Time: 21:08
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

/**
 * Exception thrown when there's a problem with the payment when submitting to the payment gateway
 *
 * @package CubicMushroom\Payments\Stripe
 */
class GatewayPaymentException extends AbstractPaymentException
{
}