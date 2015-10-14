<?php
use Codeception\Scenario;
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Step\Integration\CommandValidationWizard;
use Money\Currency;
use Money\Money;
use ValueObjects\Web\EmailAddress;

/** @var Scenario $scenario */
$I = new CommandValidationWizard($scenario);
$I->wantTo('check that a command with incorrect property types fails validation');

// Setup test subjects
$amount = 699;
$currency = 'GBP';
$cost = new Money($amount, new Currency($currency));
$token = 'iAmAToken';
$description = 'Instead of enameling slobbery rum with tuna, use six teaspoons whipped cream and one package oregano ' .
               'casserole.';
$userEmail = new EmailAddress('me@here.com');
$command = TakePaymentCommand::create($cost, $token, $description, $userEmail);
$reflectionObject = new \ReflectionObject($command);
$costProperty = $reflectionObject->getProperty('cost');
$costProperty->setAccessible(true);
$costProperty->setValue($command, $amount);
$emailProperty = $reflectionObject->getProperty('userEmail');
$emailProperty->setAccessible(true);
$emailProperty->setValue($command, (string)$userEmail);

// Perform test
$I->validateCommand($command);
$I->expectTheFollowingValidationErrors(
    [
        'cost'        => ['Please provide the cost details'],
        'userEmail'   => ['Please provide the user\'s email address'],
    ]
);