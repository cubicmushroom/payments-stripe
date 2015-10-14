<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandInterface;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
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
     *
     * @Assert\NotNull(message="Please provide the cost details")
     * @Assert\Type(type="\Money\Money", message="Please provide the cost details")
     */

    private $cost;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please provide a payment card")
     */
    private $token;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please provide a description of the payment")
     */
    private $description;

    /**
     * @var EmailAddress
     *
     * @Assert\NotNull(message="Please provide the user's email address")
     * @Assert\Type(type="\ValueObjects\Web\EmailAddress", message="Please provide the user's email address")
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
