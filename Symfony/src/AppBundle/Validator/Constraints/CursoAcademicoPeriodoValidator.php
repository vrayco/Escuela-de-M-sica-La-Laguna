<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CursoAcademicoPeriodoValidator extends ConstraintValidator
{
    public function __construct() {}

    public function validate($protocol, Constraint $constraint)
    {

        $fechaInicio = $protocol->getFechaInicio();
        $fechaFin = $protocol->getFechaFin();
        dump($constraint);

        if($fechaInicio and $fechaFin and $fechaFin <= $fechaInicio) {
            $this->context->buildViolation($constraint->message)
                ->atPath('fechaInicio')
                ->addViolation();

            $this->context->buildViolation($constraint->message)
                ->atPath('fechaFin')
                ->addViolation();
        }
    }
}