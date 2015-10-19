<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/10/15
 * Time: 21:00
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\PublicSafeMessageInterface;

/**
 * Exception throw when failed to save a successful payment
 *
 * @package CubicMushroom\Payments\Stripe
 */
class SavePaymentFailedException extends AbstractPaymentException implements PublicSafeMessageInterface
{
    /**
     * Returns a message to display to the website user
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return 'cm_payments.exception.save_payment_failed';
    }
}