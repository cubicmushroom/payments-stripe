<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandInterface;
use Money\Money;


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
     * @param Money  $cost
     * @param string $token
     * @param string $description
     *
     * @return $this
     */
    public static function create(Money $cost, $token, $description)
    {
        $takePaymentCommand = new self($cost, $token);

        $takePaymentCommand
            ->setCost($cost)
            ->setToken($token)
            ->setDescription($description);

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


    private $description;


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


}
