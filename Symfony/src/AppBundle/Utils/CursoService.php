<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Alumno;
use AppBundle\Entity\CursoAcademico;
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
        if(!$cursoActual)
            throw new NotFoundHttpException('No hay un curso actual definido');

        return $cursoActual;
    }

}