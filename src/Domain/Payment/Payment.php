<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Hexagonal\Domain\Generic\Model;
use CubicMushroom\Hexagonal\Domain\Generic\ModelInterface;
use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
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
     * @var string
     */
    protected $gatewayId;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $userEmail;

    /**
     * JSON encoded data
     *
     * @var string
     */
    protected $metaData;

    /**
     * @var bool
     */
    protected $paid;


    // -----------------------------------------------------------------------------------------------------------------
    // Constructor methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param Money        $cost        Amount & Currency of payment
     * @param string       $token       Token passed from Stripe.js
     * @param string       $description Description of what the payment is for
     * @param EmailAddress $userEmail
     * @param array        $metaData
     *
     * @return static
     */
    public static function createUnpaid(
        Money $cost,
        $token,
        $description,
        EmailAddress $userEmail,
        array $metaData = []
    ) {
        /** @var Payment $payment */
        $payment = new static();

        $payment
            ->willCosts($cost)
            ->willUseToken($token)
            ->isDescribedBy($description)
            ->belongsTo($userEmail)
            ->hasExtraData($metaData);

        return $payment;
    }


    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->hasNotBeenPaid();
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


    // -----------------------------------------------------------------------------------------------------------------
    // Intention methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param Money $cost
     *
     * @return $this
     */
    public function willCosts(Money $cost)
    {
        $this->amount   = $cost->getAmount();
        $this->currency = $cost->getCurrency()->getName();

        return $this;
    }


    /**
     * @param $token
     *
     * @return $this
     */
    public function willUseToken($token)
    {
        $this->token = $token;

        return $this;
    }


    /**
     * @param string $description
     *
     * @return $this
     */
    public function isDescribedBy($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @param EmailAddress $email
     *
     * @return $this
     */
    public function belongsTo(EmailAddress $email)
    {
        $this->userEmail = (string)$email;

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     */
    public function hasExtraData(array $data)
    {
        $this->metaData = json_encode($data);

        return $this;
    }


    /**
     * @return $this
     */
    public function hasNotBeenPaid()
    {
        $this->paid = false;

        return $this;
    }


    /**
     * @param StripePaymentId $gatewayId
     *
     * @return $this
     */
    public function hasBeenPaidWithGatewayTransaction(StripePaymentId $gatewayId)
    {
        $this->paid      = true;
        $this->gatewayId = $gatewayId->getValue();

        return $this;
    }


    /**
     * Used to extract the details to pass to the Stripe payment gateway
     *
     * @return array ['amount' => string, 'currency' => string, 'token' =. string, 'description' => string]
     */
    public function getGatewayPurchaseArray()
    {
        $metaData = ($this->metaData ? json_decode($this->metaData, true) : []);

        return [
            'amount'      => number_format(($this->amount / 100), 2),
            'currency'    => $this->currency,
            'token'       => $this->token,
            'description' => $this->description,
            'metadata'    => array_merge_recursive(
                $metaData,
                ['paymentId' => $this->id, 'userEmail' => $this->userEmail]
            ),
        ];
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getGatewayId()
    {
        return $this->gatewayId;
    }


    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }


    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
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
     * @return string
     */
    public function getMetaData()
    {
        return $this->metaData;
    }


    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }


    /**
     * @return boolean
     */
    public function isPaid()
    {
        return $this->paid;
    }
}
