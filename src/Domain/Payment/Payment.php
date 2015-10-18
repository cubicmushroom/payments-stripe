<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Hexagonal\Domain\Generic\Model;
use CubicMushroom\Hexagonal\Domain\Generic\ModelInterface;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use Money\Currency;
use Money\Money;
use ValueObjects\Web\EmailAddress;


/**
 * Class Payment
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Domain\Payment\PaymentSpec
 */
class Payment extends Model implements ModelInterface
{
    // -----------------------------------------------------------------------------------------------------------------
    // Properties
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var PaymentId
     */
    protected $id;

    /**
     * @var StripePaymentId
     */
    protected $gatewayId;

    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $metaData;


    protected $isPaid;

    /**
     * @var EmailAddress
     */
    protected $userEmail;


    // -----------------------------------------------------------------------------------------------------------------
    // Constructor
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param Money        $cost        Amount & Currency of payment
     * @param string       $token       Token passed from Stripe.js
     * @param string       $description Description of what the payment is for
     * @param EmailAddress $userEmail
     * @param array        $metaData
     */
    public function __construct(Money $cost, $token, $description, EmailAddress $userEmail, array $metaData = [])
    {
        $this->cost        = $cost;
        $this->token       = $token;
        $this->description = $description;
        $this->userEmail   = $userEmail;
        $this->metaData    = $metaData;
        $this->isPaid      = false;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Abstract Model methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Should return the class of the model's $id field value object
     *
     * @return string
     */
    protected function getIdClass()
    {
        return PaymentId::class;
    }


    /**
     * @return StripePaymentId
     */
    public function gatewayId()
    {
        return $this->gatewayId;
    }


    /**
     * @param StripePaymentId $gatewayId
     *
     * @return Payment
     */
    public function assignGatewayId(StripePaymentId $gatewayId)
    {
        $this->gatewayId = $gatewayId;

        return $this;
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


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @return EmailAddress
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }


    /**
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }


    /**
     * Used to extract the details to pass to the Stripe payment gateway
     *
     * @return array ['amount' => string, 'currency' => string, 'token' =. string, 'description' => string]
     */
    public function getGatewayPurchaseArray()
    {
        return [
            'amount'      => $this->cost->getAmount(),
            'currency'    => $this->cost->getCurrency()->getName(),
            'token'       => $this->token,
            'description' => $this->description,
            'metadata'    => array_merge_recursive(
                $this->metaData,
                ['paymentId' => $this->id->getValue(), 'userEmail' => (string)$this->userEmail]
            ),
        ];
    }


    /**
     * Returns a boolean value indicating whether the payment has been processed (or captured in Stripe terms)
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->isPaid;
    }


    /**
     *
     */
    public function markAsPaid()
    {
        $this->isPaid = true;

        return $this;
    }
}
