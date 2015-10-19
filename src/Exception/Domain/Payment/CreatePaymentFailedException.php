<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 19/10/15
 * Time: 12:40
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\PublicSafeMessageInterface;

/**
 * Class CreatePaymentFailed
 *
 * @package CubicMushroom\Payments
 */
class CreatePaymentFailedException extends PaymentFailedException implements PublicSafeMessageInterface
{

    /**
     * Returns a message to display to the website user
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return 'Your payment has not been processed.  Please try again.';
    }
}