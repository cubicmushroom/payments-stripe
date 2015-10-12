<?php

namespace spec\CubicMushroom\Payments\Stripe\Command\Payment;

use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use Money\Currency;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class TakePaymentCommandSpec
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand
 */
class TakePaymentCommandSpec extends ObjectBehavior
{
    const AMOUNT      = 999;
    const CURRENCY    = 'GBP';
    const /** @noinspection SpellCheckingInspection */
          TOKEN       = 'ashcgdocpwaelcb';
    const DESCRIPTION = 'Why does the crewmate walk?';

    /**
     * @var Money
     */
    protected $cost;

    /**
     * @var Currency
     */
    protected $currency;


    /**
     * Sets up common spec test values
     */
    public function __construct()
    {
        $this->currency = new Currency(self::CURRENCY);
        $this->cost     = new Money(self::AMOUNT, $this->currency);
    }


    function it_is_initializable()
    {
        $this->shouldHaveType(TakePaymentCommand::class);
    }


    function it_should_implement_the_command_interface()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(CommandInterface::class);
    }


    /**
     * @uses TakePaymentCommand::getCost()
     * @uses TakePaymentCommand::getToken()
     * @uses TakePaymentCommand::getDescription()
     */
    function it_should_be_initialise_through_static_builder()
    {
        $this->beConstructedThrough('create', [$this->cost, self::TOKEN, self::DESCRIPTION]);
        $this->shouldHaveType(TakePaymentCommand::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getCost()->shouldReturn($this->cost);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getToken()->shouldReturn(self::TOKEN);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getDescription()->shouldReturn(self::DESCRIPTION);
    }
}
