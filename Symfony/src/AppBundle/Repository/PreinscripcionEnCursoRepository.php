<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Curso;
use AppBundle\Entity\PreinscripcionEnCurso;
use Doctrine\ORM\EntityRepository;

/**
 * PreinscripcionEnCursoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PreinscripcionEnCursoRepository extends EntityRepository
{
    public function getPreinscripciones(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pec')
            ->from('AppBundle:PreinscripcionEnCurso', 'pec')
            ->innerJoin('pec.preinscripcion', 'p')
            ->innerJoin('pec.curso','c')
            ->where('c = :curso')
            ->setParameter('curso', $curso)
            ->getQuery()
            ->getResult();
    }

    public function getPreinscripcionesOrdenAlfabetico(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pre, p')
            ->from('AppBundle:PreinscripcionEnCurso', 'pre')
            ->innerJoin('pre.preinscripcion', 'p')
            ->innerJoin('pre.curso','c')
            ->where('c = :curso')
            ->setParameter('curso', $curso)
            ->orderBy('p.apellidos','ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPreinscripcionesOrdenLista(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pre, p')
            ->from('AppBundle:PreinscripcionEnCurso', 'pre')
            ->innerJoin('pre.preinscripcion', 'p')
            ->innerJoin('pre.curso','c')
            ->where('c = :curso')
            ->setParameter('curso', $curso)
            ->orderBy('pre.numeroLista','ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPreinscripcionesPrioritarias(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pec')
            ->from('AppBundle:PreinscripcionEnCurso', 'pec')
            ->innerJoin('pec.preinscripcion', 'p')
            ->innerJoin('pec.curso','c')
            ->where('c = :curso')
            ->andWhere('p.prioridad = TRUE')
            ->andWhere('pec.numeroLista = :sinPlaza')
            ->setParameter('curso', $curso)
            ->setParameter('sinPlaza', PreinscripcionEnCurso::SIN_PLAZA)
            ->getQuery()
            ->getResult();
    }

    public function getPreinscripcionesEmpadronados(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pec')
            ->from('AppBundle:PreinscripcionEnCurso', 'pec')
            ->innerJoin('pec.preinscripcion', 'p')
            ->innerJoin('pec.curso','c')
            ->where('c = :curso')
            ->andWhere('p.prioridad = TRUE OR p.empadronado = TRUE')
            ->andWhere('pec.numeroLista = :sinPlaza')
            ->setParameter('curso', $curso)
            ->setParameter('sinPlaza', PreinscripcionEnCurso::SIN_PLAZA)
            ->getQuery()
            ->getResult();
    }

    public function getPreinscripcionesNoEmpadronados(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pec')
            ->from('AppBundle:PreinscripcionEnCurso', 'pec')
            ->innerJoin('pec.preinscripcion', 'p')
            ->innerJoin('pec.curso','c')
            ->where('c = :curso')
            ->andWhere('p.prioridad = FALSE')
            ->andWhere('pec.numeroLista = :sinPlaza')
            ->andWhere('p.empadronado = FALSE')
            ->setParameter('curso', $curso)
            ->setParameter('sinPlaza', PreinscripcionEnCurso::SIN_PLAZA)
            ->getQuery()
            ->getResult();
    }
}
