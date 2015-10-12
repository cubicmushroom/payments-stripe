<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/10/15
 * Time: 20:59
 */

namespace CubicMushroom\Payments\Stripe\Exception\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Exception\Domain\AbstractDomainException;

/**
 * Class AbstractPaymentException
 *
 * @package CubicMushroom\Payments\Stripe
 */
abstract class AbstractPaymentException extends AbstractDomainException
{
    /**
     * @param Payment         $payment
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     *
     * @return static
     */
    public static function createWithPayment(Payment $payment, $message = "", $code = 0, \Exception $previous = null)
    {
        $exception = new static($message, $code, $previous);
        $exception->setPayment($payment);

        return $exception;
    }


    /**
     * @var Payment
     */
    protected $payment;


    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }


    /**
     * @param Payment $payment
     *
     * @return $this
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }
}