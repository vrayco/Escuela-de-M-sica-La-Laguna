<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CursoPlazasValidator extends ConstraintValidator
{
    public function __construct() {}

    public function validate($protocol, Constraint $constraint)
    {
        $numeroPlazas = $protocol->getNumeroPlazas();
        $numeroPlazasPrioritarias = $protocol->getNumeroPlazasPrioritarias();

        if($numeroPlazas and $numeroPlazasPrioritarias and $numeroPlazas < $numeroPlazasPrioritarias) {
            $this->context->buildViolation($constraint->message)
                ->atPath('numeroPlazas')
                ->addViolation();

            $this->context->buildViolation($constraint->message)
                ->atPath('numeroPlazasPrioritarias')
                ->addViolation();
        }
    }
}