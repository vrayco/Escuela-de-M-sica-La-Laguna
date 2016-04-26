<?php
namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PrematriculaValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($protocol, Constraint $constraint)
    {
        // Compruebo que al menos tenga una disciplina la inscripcion
        $prematriculaEnCursos = $protocol->getPrematriculaEnCursos();
        $nulos = true;
        foreach($prematriculaEnCursos as $p)
            if($p->getCurso())
                $nulos = false;

        if($nulos) {
            $this->context->buildViolation($constraint->message1)
                ->atPath('prematriculaEnCursos')
                ->addViolation();

        }
        else {
            // Compruebo que no hayan dos cursos iguales
            $igual = false;
            $prematriculaEnCursos = $protocol->getPrematriculaEnCursos();
            for ($i = 0; $i < sizeof($prematriculaEnCursos); $i++)
                for ($j = 0; $j < sizeof($prematriculaEnCursos); $j++)
                    if ($i != $j and $prematriculaEnCursos[$i]->getCurso() and $prematriculaEnCursos[$j]->getCurso())
                        if ($prematriculaEnCursos[$i] == $prematriculaEnCursos[$j])
                            $igual = true;
            if ($igual) {
                $this->context->buildViolation($constraint->message2)
                    ->atPath('prematriculaEnCursos')
                    ->addViolation();
            }
        }
    }
}