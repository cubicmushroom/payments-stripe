<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/10/15
 * Time: 21:00
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

/**
 * Exception throw when failed to save a successful payment
 *
 * @package CubicMushroom\Payments\Stripe
 */
class SavePaymentFailedException extends AbstractPaymentException
{
}