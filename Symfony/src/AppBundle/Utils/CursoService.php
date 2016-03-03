<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Alumno;
use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\PreinscripcionEnCurso;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CursoService
{
    private $em = null;
    private $session = null;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function getCursoActual()
    {
        $cursoActual = $this->session->get(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR);
        if(!$cursoActual) {
            $cursoActual = $this->em->getRepository('AppBundle:CursoAcademico')->findOneBy(array('enCurso' => true));
            if (!$cursoActual) {
                $cursoActual = $this->em->getRepository('AppBundle:CursoAcademico')->findOneBy(array());
                if (!$cursoActual)
                    throw new NotFoundHttpException('No hay un curso actual definido');
                else
                    $this->session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $cursoActual);
            } else
                $this->session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $cursoActual);
        } else {
            $cursoActual = $this->em->getRepository('AppBundle:CursoAcademico')->find($cursoActual->getId());
            $this->session->remove(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR);
            if($cursoActual)
                $this->session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $cursoActual);
        }

        return $cursoActual;
    }

    //
    public function refreshCursoAcademico()
    {
        $cursoActual = $this->session->get(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR);
        if($cursoActual) {
            $curso = $this->em->getRepository('AppBundle:CursoAcademico')->find($cursoActual->getId());
            if ($curso) {
                $this->session->remove(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR);
                $this->session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $curso);
            }
        }

        return $cursoActual;
    }

    public function actualizarAsignacionPlazas(Curso $curso)
    {
        $plazasAAsignar = $curso->getNumeroPlazas();
        $preinscripcionesOrdenadas = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));

        $asignadas = 0;
        foreach($preinscripcionesOrdenadas as $preinscripcion) {
            if($preinscripcion->getEstado() == PreinscripcionEnCurso::ESTADO_ACEPTADA)
                $asignadas++;
            if($asignadas < $plazasAAsignar) {
                if($preinscripcion->getEstado() != PreinscripcionEnCurso::ESTADO_ACEPTADA and $preinscripcion->getEstado() != PreinscripcionEnCurso::ESTADO_RECHAZADA) {
                    $preinscripcion->setEstado(PreinscripcionEnCurso::ESTADO_PLAZA);
                    $asignadas++;
                }
            } else {
                if($preinscripcion->getEstado() != PreinscripcionEnCurso::ESTADO_RECHAZADA)
                    $preinscripcion->setEstado(PreinscripcionEnCurso::ESTADO_RESERVA);
            }

            $this->em->persist($preinscripcion);
        }

        $this->em->flush();
    }
}