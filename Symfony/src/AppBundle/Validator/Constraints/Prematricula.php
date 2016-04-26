<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Prematricula extends Constraint
{
    public $message1 = 'La pre-matrícula debe tener al menos una disciplina';
    public $message2 = 'La pre-matrícula no puede tener dos veces las misma disciplina';
    public $message3 = 'El alumno ya tiene una pre-matrícula para este curso academico %s';

    public function validatedBy()
    {
        return 'prematricula.validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}