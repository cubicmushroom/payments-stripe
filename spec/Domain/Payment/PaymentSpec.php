<?php

namespace spec\CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Domain\Gateway\StripePaymentId;
use CubicMushroom\Payments\Stripe\Domain\Payment\Payment;
use CubicMushroom\Payments\Stripe\Domain\Payment\PaymentId;
use Money\Currency;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ValueObjects\Web\EmailAddress;


/**
 * Class PaymentSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Domain\Payment\Payment
 */
class PaymentSpec extends ObjectBehavior
{
    const AMOUNT      = 5000;
    const AMOUNT_STR  = '50.00';
    const CURRENCY    = 'GBP';
    const TOKEN       = 'ugcashdcial';
    const DESCRIPTION = 'Try chopping seaweed tart garnished with honey.';
    const USER_EMAIL  = 'bob@fish.com';
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
     * @var EmailAddress
     */
    protected $userEmail;

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
        $this->userEmail = new EmailAddress(self::USER_EMAIL);
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
        $this->beConstructedThrough(
            'createUnpaid',
            [$this->cost, self::TOKEN, self::DESCRIPTION, $this->userEmail, $this->metaData]
        );
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
     * @uses Payment::getUserEmail()
     *
     * @todo - Replace getXxx() with xxx() methods
     */
    function it_returns_all_the_details()
    {
        /** @var self|Payment $this */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getAmount()->shouldReturn(self::AMOUNT);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCurrency()->shouldReturn(self::CURRENCY);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getDescription()->shouldReturn(self::DESCRIPTION);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getUserEmail()->shouldReturn(self::USER_EMAIL);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getToken()->shouldReturn(self::TOKEN);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getMetaData()->shouldReturn(json_encode($this->metaData));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->isPaid()->shouldReturn(false);
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
                'amount'      => self::AMOUNT_STR,
                'currency'    => self::CURRENCY,
                'token'       => self::TOKEN,
                'description' => self::DESCRIPTION,
                'metadata'    => array_merge(
                    $this->metaData,
                    ['paymentId' => self::ID, 'userEmail' => self::USER_EMAIL]
                ),
            ]
        );
    }


    function it_can_be_marked_as_paid()
    {
        /** @var self|Payment $this */
        $stripeId = new StripePaymentId(self::GATEWAY_ID);
        $this->hasBeenPaidWithGatewayTransaction($stripeId);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->isPaid()->shouldReturn(true);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getGatewayId()->shouldBeLike($stripeId);
    }
}
