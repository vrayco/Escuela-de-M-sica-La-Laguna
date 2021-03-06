<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use Doctrine\ORM\EntityRepository;

/**
 * PreinscripcionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PreinscripcionRepository extends EntityRepository
{
    public function getPreinscripciones(CursoAcademico $cursoAcademico, $filter)
    {
        $identificador = $filter['identificador'];
        $dni = $filter['dni'];
        $nombre = $filter['nombre'];
        $apellidos = $filter['apellidos'];
        $fechaNacimiento = $filter['fecha_nacimiento'];

        $qb = $this->getEntityManager()
            ->createQueryBuilder();
        $query = $qb->select('p')
            ->from('AppBundle:Preinscripcion', 'p')
            ->innerJoin('p.preinscripcionEnCursos','pre')
            ->innerJoin('pre.curso','c')
            ->innerJoin('c.cursoAcademico','ca')
            ->where('ca = :cursoAcademico')
            ->setParameter('cursoAcademico', $cursoAcademico);
        if($identificador)
            $query->andWhere(
                $qb->expr()->like('p.id', ':identificador')
            )
                ->setParameter('identificador','%'.$identificador.'%');
        if($dni)
            $query->andWhere(
                $qb->expr()->like('p.dni', ':dni')
            )
                ->setParameter('dni','%'.$dni.'%');
        if($nombre)
            $query->andWhere(
                $qb->expr()->like('p.nombre', ':nombre')
            )
                ->setParameter('nombre','%'.$nombre.'%');
        if($apellidos)
            $query->andWhere(
                $qb->expr()->like('p.apellidos', ':apellidos')
            )
                ->setParameter('apellidos','%'.$apellidos.'%');
        if($fechaNacimiento)
            $query->andWhere('p.fechaNacimiento = :fechaNacimiento')
                ->setParameter('fechaNacimiento', $fechaNacimiento);

        $query->orderBy('p.id','DESC');

        return $query->getQuery()->getResult();
    }

    public function getTotal(CursoAcademico $cursoAcademico)
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(DISTINCT p.id) as total')
            ->from('AppBundle:Preinscripcion', 'p')
            ->innerJoin('p.preinscripcionEnCursos', 'pre')
            ->innerJoin('pre.curso','c')
            ->innerJoin('c.cursoAcademico','cu')
            ->where('cu = :cursoAcademico')
            ->setParameter('cursoAcademico', $cursoAcademico)
            ->getQuery()
            ->getResult();

        return $result[0]['total'];
    }

}
