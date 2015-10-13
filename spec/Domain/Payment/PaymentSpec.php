<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use Money\Currency;
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
    const AMOUNT      = 999;
    const CURRENCY    = 'GBP';
    const TOKEN       = 'ugcashdcial';
    const DESCRIPTION = 'Try chopping seaweed tart garnished with honey.';
    const GATEWAY_ID  = 'ch_bflco298h2932bc2c02';

    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var array
     */
    protected $metaData;

    /**
     * @var StripePaymentId
     */
    protected $gatewayId;


    /**
     * Sets up common spec properties
     */
    public function __construct()
    {
        $this->currency = new Currency(self::CURRENCY);
        $this->cost     = new Money(self::AMOUNT, $this->currency);
        $this->metaData = [
            1   => 'abc',
            'a' => 'xyz',
        ];
        $this->gatewayId = new StripePaymentId(self::GATEWAY_ID);
    }


    /**
     * @uses Payment::__construct()
     */
    public function let()
    {
        // @todo - Make $token into a value object
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection SpellCheckingInspection */
        $this->beConstructedWith($this->cost, self::TOKEN, self::DESCRIPTION, $this->metaData);
    }


    /**
     * @uses Payment::__construct()
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }


    /**
     * @uses Payment::getAmount()
     * @uses Payment::getCurrency()
     * @uses Payment::getCost()
     * @uses Payment::getDescription()
     */
    function it_returns_the_amount_and_currency()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAmount()->shouldReturn(self::AMOUNT);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCurrency()->shouldReturn($this->currency);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCost()->shouldReturn($this->cost);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getDescription()->shouldReturn(self::DESCRIPTION);
    }


    /**
     * @uses Payment::getToken()
     */
    function it_returns_the_token()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getToken()->shouldReturn(self::TOKEN);
    }


    /**
     * @uses Payment::getDescription()
     */
    function it_returns_the_description()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getDescription()->shouldReturn(self::DESCRIPTION);
    }


    /**
     * @uses Payment::getMetaData()
     */
    function it_returns_metadata()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getMetaData()->shouldReturn($this->metaData);
    }


    /**
     * @uses Payment::assignGatewayId()
     * @uses Payment::gatewayId()
     */
    function it_can_have_its_gateway_id_set_and_retrieved()
    {
        $this->assignGatewayId($this->gatewayId)->shouldBeAnInstanceOf(Payment::class);
        $this->gatewayId()->shouldReturn($this->gatewayId);
    }


    /**
     * @uses Payment::getGatewayPurchaseArray();
     */
    function it_returns_an_array_of_details_for_the_gateway_purchase_call()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getGatewayPurchaseArray()->shouldReturn(
            [
                'amount'      => self::AMOUNT,
                'currency'    => self::CURRENCY,
                'token'       => self::TOKEN,
                'description' => self::DESCRIPTION,
            ]
        );
    }
}
