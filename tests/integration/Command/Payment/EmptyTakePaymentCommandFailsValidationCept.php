<?php
use CubicMushroom\Payments\Stripe\Command\Payment\TakePaymentCommand;
use CubicMushroom\Payments\Stripe\Step\Integration\CommandValidationWizard;

/** @var \Codeception\Scenario $scenario */
$I = new CommandValidationWizard($scenario);
$I->wantTo('check an empty take payment command does not validate');

// Setup test subjects
$command = new TakePaymentCommand();

// Perform tests
$I->validateCommand($command);
$I->expectTheFollowingValidationErrors(
    [
        'cost'        => ['Please provide the cost details'],
        'token'       => ['Please provide a payment card'],
        'description' => ['Please provide a description of the payment'],
        'userEmail'   => ['Please provide the user\'s email address'],
    ]
);