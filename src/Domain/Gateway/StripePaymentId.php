<?php

namespace CubicMushroom\Payments\Stripe\Domain\Gateway;

/**
 * Class StripePaymentId
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentIdSpec
 */
class StripePaymentId
{
    // -----------------------------------------------------------------------------------------------------------------
    // Properties
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $value;


    // -----------------------------------------------------------------------------------------------------------------
    // Constructor
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = (string)$value;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
