<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DisciplinaEdad extends Constraint
{
    public $message = 'La edad máxima no puede ser inferior a la mínima';

    public function validatedBy()
    {
        return 'disciplina.edad.validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}