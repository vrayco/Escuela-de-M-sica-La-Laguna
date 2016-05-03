<?php

namespace AppBundle\Repository;

use AppBundle\Entity\CursoAcademico;
use Doctrine\ORM\EntityRepository;

/**
 * MatriculaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MatriculaRepository extends EntityRepository
{
    public function getMatriculas(CursoAcademico $cursoAcademico = null, $filter)
    {
        if(!$cursoAcademico)
            return null;

        $identificador = $filter['identificador'];
        $curso = $filter['curso'];
        $expedienteLetra = $filter['expedienteLetra'];
        $expedienteNumero = $filter['expedienteNumero'];
        $dni = $filter['dni'];
        $nombre = $filter['nombre'];
        $apellidos = $filter['apellidos'];
        $fechaNacimiento = $filter['fecha_nacimiento'];

        $qb = $this->getEntityManager()
            ->createQueryBuilder();
        $query = $qb->select('m')
            ->from('AppBundle:Matricula', 'm')
            ->innerJoin('m.alumno','a')
            ->innerJoin('m.curso','c')
            ->innerJoin('c.cursoAcademico','ca')
            ->innerJoin('c.disciplina','d')
            ->where('ca = :cursoAcademico')
            ->setParameter('cursoAcademico', $cursoAcademico);
        if($identificador)
            $query->andWhere(
                $qb->expr()->like('m.id', ':identificador')
            )
                ->setParameter('identificador','%'.$identificador.'%');
        if($curso)
            $query->andWhere('c.id = :curso')
                ->setParameter('curso', $curso);
        if($expedienteLetra)
            $query->andWhere(
                $qb->expr()->like('a.expedienteLetra', ':expedienteLetra')
            )
                ->setParameter('expedienteLetra','%'.$expedienteLetra.'%');
        if($expedienteNumero)
            $query->andWhere(
                $qb->expr()->like('a.expedienteNumero', ':expedienteNumero')
            )
                ->setParameter('expedienteNumero','%'.$expedienteNumero.'%');
        if($dni)
            $query->andWhere(
                $qb->expr()->like('a.dni', ':dni')
            )
                ->setParameter('dni','%'.$dni.'%');
        if($nombre)
            $query->andWhere(
                $qb->expr()->like('a.nombre', ':nombre')
            )
                ->setParameter('nombre','%'.$nombre.'%');
        if($apellidos)
            $query->andWhere(
                $qb->expr()->like('a.apellidos', ':apellidos')
            )
                ->setParameter('apellidos','%'.$apellidos.'%');
        if($fechaNacimiento)
            $query->andWhere('a.fechaNacimiento = :fechaNacimiento')
                ->setParameter('fechaNacimiento', $fechaNacimiento);

        $query->orderBy('m.id','DESC');

        return $query->getQuery()->getResult();
    }

    public function getTotal(CursoAcademico $cursoAcademico = null)
    {
        if(!$cursoAcademico)
            return 0;

        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(DISTINCT m.id) as total')
            ->from('AppBundle:Matricula', 'm')
            ->innerJoin('m.curso', 'c')
            ->innerJoin('c.cursoAcademico','cu')
            ->where('cu = :cursoAcademico')
            ->setParameter('cursoAcademico', $cursoAcademico)
            ->getQuery()
            ->getResult();

        return $result[0]['total'];
    }
}
