<?php

namespace CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Payments\Stripe\Command\CommandInterface;
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
     *
     * @return $this
     */
    public static function create(Money $cost, $token)
    {
        $takePaymentCommand = new self($cost, $token);

        $takePaymentCommand
            ->setCost($cost)
            ->setToken($token);

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
}
