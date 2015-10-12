<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/10/15
 * Time: 20:08
 */

namespace CubicMushroom\Payments\Stripe\Event;

/**
 * Contains the names of all the package's event names
 *
 * @package CubicMushroom\Payments\Stripe
 */
class Events
{

    const TAKE_PAYMENT_SUCCESS = 'cm_stripe_payments_take_payment_success';

    const TAKE_PAYMENT_FAILURE = 'cm_stripe_payments_take_payment_failure';
}