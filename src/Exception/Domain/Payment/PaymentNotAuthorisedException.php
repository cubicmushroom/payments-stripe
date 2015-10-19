<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 19/10/15
 * Time: 05:28
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\PublicSafeMessageInterface;

/**
 * Exception thrown when a payment is rejected by the gateway
 *
 * IMPORTANT NOTE…
 * The message on this class should only ever bet set to the Stripe response error for failed payments
 *
 * @package CubicMushroom\Payments
 */
class PaymentNotAuthorisedException extends AbstractPaymentException implements PublicSafeMessageInterface
{
    /**
     * Returns a message to display to the website user
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return 'Your payment was rejected.  ' . $this->getMessage();
    }
}