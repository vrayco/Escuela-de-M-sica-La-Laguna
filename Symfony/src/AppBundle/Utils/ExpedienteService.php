<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Alumno;
use Doctrine\ORM\EntityManager;

class ExpedienteService
{
    private $em = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setExpediente(Alumno $alumno)
    {
        $cursoActual = $this->em->getRepository('AppBundle:CursoAcademico')->getCursoEnCurso();

        $max = $this->em->getRepository('AppBundle:Alumno')->getNumeroUltimoExpediente($cursoActual);

        $alumno->setExpedienteLetra($cursoActual->getPrefijoExpediente());
        $alumno->setExpedienteNumero($max+1);

        return $alumno;
    }

}