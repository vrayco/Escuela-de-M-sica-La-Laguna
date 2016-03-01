<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CursoAcademicoPeriodo extends Constraint
{
    public $message = 'El periodo es incorrecto';

    public function validatedBy()
    {
        return 'cursoacademico.periodo.validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}