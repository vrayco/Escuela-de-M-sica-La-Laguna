<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Preinscripcion extends Constraint
{
    public $message1 = 'La pre-inscripción debe tener al menos una disciplina';
    public $message2 = 'La pre-inscripción no puede tener dos veces las misma disciplina';
    public $message3 = 'La pre-inscripción contiene disciplinas incompatibles';
    public $message4 = 'La disciplina %s es incompatible con la edad del alumno';
    public $message5 = 'Es incompatible que el alumno tenga prioridad y no esté empadronado';

    public function validatedBy()
    {
        return 'preinscripcion.validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}