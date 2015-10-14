<?php
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Step\Integration\CommandValidationWizard;
use Money\Currency;
use Money\Money;
use ValueObjects\Web\EmailAddress;

/** @var \Codeception\Scenario $scenario */
$I = new CommandValidationWizard($scenario);
$I->wantTo('check a valid command passes');

// Setup test subjects
$amount = 699;
$currency = 'GBP';
$cost = new Money($amount, new Currency($currency));
$token = 'iAmAToken';
$description = 'Instead of enameling slobbery rum with tuna, use six teaspoons whipped cream and one package oregano ' .
               'casserole.';
$userEmail = new EmailAddress('me@here.com');
$command = TakePaymentCommand::create($cost, $token, $description, $userEmail);

// Perform tests
$I->validateCommand($command);
$I->expectNoValidationErrors();