<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PreinscripcionValidator extends ConstraintValidator
{
    public function __construct() {}

    public function validate($protocol, Constraint $constraint)
    {
        dump($protocol->getPrioridad() and !$protocol->getEmpadronado());
        // Compruebo que si es alumno que prioridad tambien está empadronado
        if($protocol->getPrioridad() and !$protocol->getEmpadronado())
            $this->context->buildViolation($constraint->message5)
                ->atPath('empadronado')
                ->addViolation();

        // Compruebo que al menos tenga una disciplina la inscripcion
        $preinscripcionEnCursos = $protocol->getPreinscripcionEnCursos();
        $nulos = true;
        foreach($preinscripcionEnCursos as $p)
            if($p->getCurso())
                $nulos = false;

        if($nulos) {
            $this->context->buildViolation($constraint->message1)
                ->atPath('preinscripcionEnCursos')
                ->addViolation();

        }
        else {

            // Compruebo que no hayan dos cursos iguales
            $igual = false;
            $preinscripcionEnCursos = $protocol->getPreinscripcionEnCursos();
            for ($i = 0; $i < sizeof($preinscripcionEnCursos); $i++)
                for ($j = 0; $j < sizeof($preinscripcionEnCursos); $j++)
                    if ($i != $j and $preinscripcionEnCursos[$i]->getCurso() and $preinscripcionEnCursos[$j]->getCurso())
                        if ($preinscripcionEnCursos[$i] == $preinscripcionEnCursos[$j])
                            $igual = true;
            if ($igual) {
                $this->context->buildViolation($constraint->message2)
                    ->atPath('preinscripcionEnCursos')
                    ->addViolation();
            } else {

                // Compruebo que no hayan grupos de disciplinas incompatibles
                $preinscripcionEnCursos = $protocol->getPreinscripcionEnCursos();

                // 1 Paso: ¿Es algun grupo de disciplina excluyente?
                $excluyente = false;
                foreach ($preinscripcionEnCursos as $p)
                    if ($p->getCurso())
                        if ($p->getCurso()->getDisciplina()->getDisciplinaGrupo()->getIncompatibleConOtro())
                            $excluyente = true;

                // 2 Paso: ¿Hay inscripciones de diferente grupo disciplina?
                $diferenteGrupoDisciplina = false;
                for ($i = 0; $i < sizeof($preinscripcionEnCursos); $i++)
                    for ($j = 0; $j < sizeof($preinscripcionEnCursos); $j++)
                        if ($i != $j and $preinscripcionEnCursos[$i]->getCurso() and $preinscripcionEnCursos[$j]->getCurso())
                            if ($preinscripcionEnCursos[$i]->getCurso()->getDisciplina()->getDisciplinaGrupo() != $preinscripcionEnCursos[$j]->getCurso()->getDisciplina()->getDisciplinaGrupo())
                                $diferenteGrupoDisciplina = true;

                if ($excluyente and $diferenteGrupoDisciplina) {
                    $this->context->buildViolation($constraint->message3)
                        ->atPath('preinscripcionEnCursos')
                        ->addViolation();
                } else {
                    // Comprobamos que la edad del alumno sea validada para la permitida por el curso
                    $preinscripcionEnCursos = $protocol->getPreinscripcionEnCursos();
                    foreach($preinscripcionEnCursos as $p)
                        if($p->getCurso()) {
                            $fechaNacimiento = $protocol->getFechaNacimiento();
                            $edadMinima = $p->getCurso()->getDisciplina()->getEdadMinima();
                            $edadMaxima = $p->getCurso()->getDisciplina()->getEdadMaxima();

                            if($edadMinima) {
                                $fechaLimite = new \DateTime('now');
                                $fechaLimite->setDate($fechaLimite->format('Y')-$edadMinima,12,31);
                                $fechaLimite->setTime(0,0,0);
                                if($fechaLimite < $fechaNacimiento) {
                                    $this->context->buildViolation(sprintf($constraint->message4, $p->getCurso()))
                                        ->atPath('preinscripcionEnCursos')
                                        ->addViolation();
                                    break;
                                }

                            }

                            if($edadMaxima) {
                                $fechaLimite = new \DateTime('now');
                                $fechaLimite->setDate($fechaLimite->format('Y')-$edadMaxima,1,1);
                                $fechaLimite->setTime(0,0,0);
                                if($fechaLimite > $fechaNacimiento) {
                                    $this->context->buildViolation(sprintf($constraint->message4, $p->getCurso()))
                                        ->atPath('preinscripcionEnCursos')
                                        ->addViolation();
                                    break;
                                }

                            }

                        }


                }
            }
        }
    }
}