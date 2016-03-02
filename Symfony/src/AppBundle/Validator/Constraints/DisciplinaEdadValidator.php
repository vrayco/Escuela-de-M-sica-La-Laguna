<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DisciplinaEdadValidator extends ConstraintValidator
{
    public function __construct() {}

    public function validate($protocol, Constraint $constraint)
    {
        $edadMinima = $protocol->getEdadMinima();
        $edadMaxima = $protocol->getEdadMaxima();

        if($edadMinima and $edadMaxima and $edadMinima > $edadMaxima) {
            $this->context->buildViolation($constraint->message)
                ->atPath('edadMinima')
                ->addViolation();

            $this->context->buildViolation($constraint->message)
                ->atPath('edadMaxima')
                ->addViolation();
        }
    }
}