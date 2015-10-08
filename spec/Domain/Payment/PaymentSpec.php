<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class PaymentSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Domain\Payment\Payment
 */
class PaymentSpec extends ObjectBehavior
{
    public function let(Money $cost)
    {
        // @todo - Make $token into a value object
        $this->beConstructedWith($cost, $token = 'ugcashdcial');
    }


    /**
     * @uses Payment::__construct()
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }


}
