<?php
namespace CubicMushroom\Payments\Stripe\Step\Integration;

use Codeception\Scenario;
use CubicMushroom\Hexagonal\Command\CommandInterface;
use CubicMushroom\Payments\Stripe\CodeWizard;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandValidationWizard extends CodeWizard
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ConstraintViolationListInterface|ConstraintViolationInterface[]
     */
    private $validationErrors;


    /**
     * ValidateCommand constructor.
     *
     * @param Scenario $scenario
     */
    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $validatorBuilder = Validation::createValidatorBuilder();
        $validatorBuilder->enableAnnotationMapping();

        $autoloader = require realpath(__DIR__ . '/../../../../vendor/autoload.php');
        AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);
        $this->validator = $validatorBuilder->getValidator();
    }


    public function validateCommand(CommandInterface $command)
    {
        $I = $this;
        $I->validationErrors = $I->validator->validate($command);
    }


    public function expectNoValidationErrors()
    {
        $I = $this;
        $I->assertEmpty($this->validationErrors);
    }


    /**
     * @param array $expectedErrors Multi-dimensional array of expected errors with the top level key being the property
     *                              name, and the value being an array of expected error messages
     */
    public function expectTheFollowingValidationErrors(array $expectedErrors)
    {
        $I = $this;

        $actualErrors = [];
        foreach ($this->validationErrors as $actualError) {
            $actualErrors[$actualError->getPropertyPath()][] = $actualError->getMessage();
        }

        array_multisort($actualErrors);
        array_multisort($expectedErrors);

        $I->assertEquals($expectedErrors, $actualErrors);
    }

}