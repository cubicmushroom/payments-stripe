<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 19/10/15
 * Time: 13:09
 */

namespace CubicMushroom\Payments\Stripe\Exception;

/**
 * Interface to indicate that this exception has a publicly safe message
 *
 * @package CubicMushroom\Payments\Stripe
 */
interface PublicSafeMessageInterface
{
    /**
     * Returns a message to display to the website user
     *
     * @return string
     */
    public function getPublicMessage();
}