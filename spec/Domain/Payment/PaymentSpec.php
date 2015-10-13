<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
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
    const ID          = 126;

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
     * @var PaymentId
     */
    protected $id;


    /**
     * Sets up common spec properties
     */
    public function __construct()
    {
        $this->currency  = new Currency(self::CURRENCY);
        $this->cost      = new Money(self::AMOUNT, $this->currency);
        $this->metaData  = [
            1   => 'abc',
            'a' => 'xyz',
        ];
        $this->id        = new PaymentId(self::ID);
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
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assignGatewayId($this->gatewayId)->shouldBeAnInstanceOf(Payment::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->gatewayId()->shouldReturn($this->gatewayId);
    }


    /**
     * @uses Payment::assignId();
     * @uses Payment::getGatewayPurchaseArray();
     */
    function it_returns_an_array_of_details_for_the_gateway_purchase_call()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assignId($this->id);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getGatewayPurchaseArray()->shouldReturn(
            [
                'amount'      => self::AMOUNT,
                'currency'    => self::CURRENCY,
                'token'       => self::TOKEN,
                'description' => self::DESCRIPTION,
                'metadata'    => array_merge($this->metaData, ['paymentId' => self::ID]),
            ]
        );
    }


    function it_can_have_its_id_assigned_and_retrieved()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assignId($this->id)->shouldBeAnInstanceOf(Payment::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->id()->shouldReturn($this->id);
    }


    function its_id_should_be_immutable_once_set()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assignId($this->id)->shouldBeAnInstanceOf(Payment::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldThrow()->during('assignId', [$this->id]);
    }


    /**
     * @uses Payment::isPaid()
     */
    function it_should_default_to_unpaid()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->isPaid()->shouldReturn(false);
    }


    /**
     * @uses Payment::markAsPaid()
     * @uses Payment::isPaid()
     */
    function it_should_be_possible_to_mark_as_paid()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->markAsPaid()->shouldReturnAnInstanceOf(Payment::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->isPaid()->shouldReturn(true);
    }
}
