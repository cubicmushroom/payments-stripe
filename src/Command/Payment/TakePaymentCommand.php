<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandInterface;
use Money\Money;
use ValueObjects\Web\EmailAddress;


/**
 * Class TakePaymentCommand
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommandSpec
 */
class TakePaymentCommand implements CommandInterface
{
    // -----------------------------------------------------------------------------------------------------------------
    // Static builder methods
    // -----------------------------------------------------------------------------------------------------------------


    /**
     * @param Money        $cost
     * @param string       $token
     * @param string       $description
     *
     * @param EmailAddress $userEmail
     *
     * @return $this
     */
    public static function create(Money $cost, $token, $description, EmailAddress $userEmail)
    {
        $takePaymentCommand = new self($cost, $token);

        $takePaymentCommand
            ->setCost($cost)
            ->setToken($token)
            ->setDescription($description)
            ->setUserEmail($userEmail);

        return $takePaymentCommand;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Properties
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var Money
     */

    private $cost;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $description;

    /**
     * @var EmailAddress
     */
    private $userEmail;


    /**
     * TakePaymentCommand constructor.
     */
    public function __construct()
    {
    }


    /**
     * @return Money
     */
    public function getCost()
    {
        return $this->cost;
    }


    /**
     * @param Money $cost
     *
     * @return $this
     */
    public function setCost(Money $cost)
    {
        $this->cost = $cost;

        return $this;
    }


    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @return EmailAddress
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }


    /**
     * @param EmailAddress $userEmail
     *
     * @return TakePaymentCommand
     */
    public function setUserEmail(EmailAddress $userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }


}
