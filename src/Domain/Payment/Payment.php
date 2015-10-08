<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use Money\Currency;
use Money\Money;


/**
 * Class Payment
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Domain\Payment\PaymentSpec
 */
class Payment
{
    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var string
     */
    private $token;


    /**
     * @param Money  $cost  Amount & Currency of payment
     * @param string $token Token passed from Stripe.js
     */
    public function __construct(Money $cost, $token)
    {
        $this->cost  = $cost;
        $this->token = $token;
    }


    /**
     * Returns the amount of the payment a an integer
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->cost->getAmount();
    }


    /**
     * Returns the currency part of the $cost property
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->cost->getCurrency();
    }


    /**
     * @return Money
     */
    public function getCost()
    {
        return $this->cost;
    }


    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
