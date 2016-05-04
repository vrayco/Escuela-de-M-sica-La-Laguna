<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\PreinscripcionEnCurso;
use AppBundle\Entity\Prematricula;
use AppBundle\Entity\PrematriculaEnCurso;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;

class SorteoPlazasPrematriculaV2Service
{
    const NOMBRE_SORTEO = "SORTEO PREMATRICULAS";
    const TIEMPO_EJECUCION_MAX = 600;

    private $em;
    private $logger;
    private $posicionLista;

    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->posicionLista = 1;
    }

    public function celebrarSorteo(CursoAcademico $cursoAcademico)
    {
        set_time_limit(self::TIEMPO_EJECUCION_MAX);

        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Inicio del %s del curso %s a las %s', self::NOMBRE_SORTEO, $cursoAcademico->__toString(), $ahora->format('d/m/Y H:i:s'))
        );

        $semilla = $this->generarSemilla();
        srand($semilla);

        $this->logger->addInfo(
            sprintf('Incorporamos la semilla %f al generador de números aleatorios.', $semilla)
        );

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $this->logger->addInfo(
            sprintf('Número total de especialidades: %d', count($cursos))
        );

        foreach($cursos as $curso)
            if ($curso->getEntraEnSorteoPrematricula()) {
                $this->generarListas($curso);
                $this->asignarPlazas($curso);
            }
        
        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin del %s a las %s', self::NOMBRE_SORTEO, $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->setPrematriculasGeneracionDeListas($ahora);
        $this->em->flush();
    }

    public function generarListas(Curso $curso)
    {
        $this->inicializarListado($curso);
        $this->posicionLista = 1;
        $disciplina = $curso->getDisciplina();
        $numeroPlazas = $curso->getNumeroPlazasPrematricula();
        $prematriculas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso);

        $this->logger->addInfo(
            sprintf('Sorteo de la especialidad %s', strtoupper($disciplina->getNombre(). ' (' . $disciplina->getDisciplinaGrupo()->getNombre().')'))
        );
        $this->logger->addInfo(
            sprintf('Número plazas a sortear %d', $numeroPlazas)
        );

        $this->logger->addInfo(
            sprintf('Número candidatos %d', count($prematriculas))
        );

        // 1/3 SORTEO: PREFERENCIA 1
        $prematriculasPreferencia1 = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso, 1);
        $this->logger->addInfo(
            sprintf('SORTEO DE PREFERENCIA 1: %d candidatos', count($prematriculasPreferencia1))
        );
        $this->ejecutarSorteo(count($prematriculasPreferencia1), $prematriculasPreferencia1);

        // 2/3 SORTEO: PREFERENCIA 2
        $prematriculasPreferencia2 = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso, 2);
        $this->logger->addInfo(
            sprintf('SORTEO PREFERENCIA 2: %d candidatos', count($prematriculasPreferencia2))
        );
        $this->ejecutarSorteo(count($prematriculasPreferencia2), $prematriculasPreferencia2);

        // 3/3 SORTEO: PREFERENCIA 3
        $prematriculasPreferencia3 = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso, 3);
        $this->logger->addInfo(
            sprintf('SORTEO PREFERENCIA 3: %d candidatos', count($prematriculasPreferencia3))
        );
        $this->ejecutarSorteo(count($prematriculasPreferencia3), $prematriculasPreferencia3);

    }

    public function asignarPlazas(Curso $curso)
    {
        $plazasAAsignar = $curso->getNumeroPlazasPrematricula();
        $prematriculasOrdenadas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));

        $asignadas = 0;
        foreach($prematriculasOrdenadas as $prematricula) {
            if($asignadas < $plazasAAsignar and $prematricula->getEstado() != PrematriculaEnCurso::ESTADO_DESCARTADA) {
                $prematricula->setEstado(PrematriculaEnCurso::ESTADO_PLAZA);
                $asignadas++;
                
                // Al asignar una plaza, las otras prematriculas del alumnos pasa al estado ESTADO_DESCARTADA
                $prematriculasAlumno = $prematricula->getPrematricula()->getPrematriculaEnCursos();
                foreach ($prematriculasAlumno as $p)
                    if($p != $prematricula) {
                        $p->setEstado(PrematriculaEnCurso::ESTADO_DESCARTADA);
                        $this->em->persist($p);
                    }
            } else
                $prematricula->setEstado(PrematriculaEnCurso::ESTADO_SIN_PLAZA);

            $this->em->persist($prematricula);
        }

        $this->em->flush();
    }

    private function ejecutarSorteo($numeroPlazas, $prematriculas)
    {
        $plazasAsignadas = 0;
        for($i = 0; $i < $numeroPlazas; $i++) {
            $asignada = false;
            while(!$asignada AND ($plazasAsignadas < count($prematriculas))) {
                $numeroAleatorio = mt_rand(0, count($prematriculas)-1);
                $prematricula = $prematriculas[$numeroAleatorio];
                if($prematricula->getNumeroLista() == PrematriculaEnCurso::SIN_PLAZA) {
                    $prematricula->setNumeroLista($this->posicionLista);
                    $this->em->persist($prematricula);
                    $this->posicionLista++;
                    $plazasAsignadas++;
                    $asignada = true;
                }
            }

        }

        $this->em->flush();
    }

    public function inicializarSorteo(CursoAcademico $cursoAcademico)
    {
        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Inicializo el sorteo a las %s', $ahora->format('d/m/Y H:i:s'))
        );

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        foreach($cursos as $curso)
            if($curso->getEntraEnSorteoPrematricula())
                $this->inicializarListado($curso);

        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin de la incialización a las %s', $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->setPrematriculasGeneracionDeListas(null);
        $this->em->flush();
    }

    private function inicializarListado(Curso $curso)
    {
        $prematriculas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso);
        foreach($prematriculas as $prematricula) {
            $prematricula->setNumeroLista(PrematriculaEnCurso::SIN_PLAZA);
            $prematricula->setEstado(PrematriculaEnCurso::ESTADO_PREMATRICULADO);
            $this->em->persist($prematricula);
        }
        $this->em->flush();
    }

    private function generarSemilla()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

}