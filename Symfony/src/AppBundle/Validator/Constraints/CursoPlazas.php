<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CursoPlazas extends Constraint
{
    public $message = 'El número de plazas prioritarias debe ser menor que el total';

    public function validatedBy()
    {
        return 'curso.plazas.validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}