<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Curso;
use AppBundle\Entity\Prematricula;
use Doctrine\ORM\EntityRepository;

/**
 * PrematriculaEnCursoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrematriculaEnCursoRepository extends EntityRepository
{
    public function getPrematriculas(Curso $curso, $preferencia = null)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pec')
            ->from('AppBundle:PrematriculaEnCurso', 'pec')
            ->innerJoin('pec.prematricula', 'p')
            ->innerJoin('pec.curso','c')
            ->where('c = :curso')
            ->setParameter('curso', $curso);

        if($preferencia)
            $query->andWhere('pec.preferencia = :preferencia')
                ->setParameter('preferencia', $preferencia);

        return $query
            ->orderBy('p.createdAt','ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPrematriculasOrdenAlfabetico(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pre, p')
            ->from('AppBundle:PrematriculaEnCurso', 'pre')
            ->innerJoin('pre.prematricula', 'p')
            ->innerJoin('pre.curso','c')
            ->innerJoin('p.alumno','a')
            ->where('c = :curso')
            ->setParameter('curso', $curso)
            ->orderBy('a.apellidos','ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPrematriculaEnCurso(Prematricula $prematricula, $preferencia)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pre')
            ->from('AppBundle:PrematriculaEnCurso', 'pre')
            ->innerJoin('pre.prematricula', 'p')
            ->where('p = :prematricula')
            ->andWhere('pre.preferencia = :preferencia')
            ->setParameter('prematricula', $prematricula)
            ->setParameter('preferencia', $preferencia)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPrematriculasOrdenLista(Curso $curso)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pre, p')
            ->from('AppBundle:PrematriculaEnCurso', 'pre')
            ->innerJoin('pre.prematricula', 'p')
            ->innerJoin('pre.curso','c')
            ->where('c = :curso')
            ->setParameter('curso', $curso)
            ->orderBy('pre.numeroLista','ASC')
            ->getQuery()
            ->getResult();
    }
}
