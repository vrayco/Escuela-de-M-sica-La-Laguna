<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\PreinscripcionEnCurso;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;

class SorteoPlazasService
{
    const NOMBRE_SORTEO = "SORTEO PREINSCRIPCIONES";
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

        $cursos = $this->em->getRepository('AppBundle:Curso')->getCursosEntraEnSorteo($cursoAcademico);

        $this->logger->addInfo(
            sprintf('Número total de especialidades: %d', count($cursos))
        );

        foreach($cursos as $curso) {
            $this->generarListas($curso);
            $this->asignarPlazas($curso);
        }

        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin del %s a las %s', self::NOMBRE_SORTEO, $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->setGeneracionDeListas($ahora);
        $this->em->flush();
    }

    public function generarListas(Curso $curso)
    {
        $this->inicializarListado($curso);
        $this->posicionLista = 1;
        $disciplina = $curso->getDisciplina();
        $numeroPlazas = $curso->getNumeroPlazas();
        $numeroPlazasPrioritarias = $curso->getNumeroPlazasPrioritarias();
        $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripciones($curso);

        $this->logger->addInfo(
            sprintf('Sorteo de la especialidad %s', strtoupper($disciplina->getNombre(). ' (' . $disciplina->getDisciplinaGrupo()->getNombre().')'))
        );
        $this->logger->addInfo(
            sprintf('Número plazas a sortear %d', $numeroPlazas)
        );
        $this->logger->addInfo(
            sprintf('Número plazas prioritarias a sortear %d', $numeroPlazasPrioritarias)
        );
        $this->logger->addInfo(
            sprintf('Número candidatos %d', sizeof($preinscripciones))
        );

        // 1/3 SORTEO: PRIORIDAD
        $preinscripcionesPrioridad = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesPrioritarias($curso);
        $this->logger->addInfo(
            sprintf('SORTEO DE PRIORIDAD: %d candidatos para %d plazas prioritarias', sizeof($preinscripcionesPrioridad), $numeroPlazasPrioritarias)
        );
        $this->ejecutarSorteo($numeroPlazasPrioritarias, $preinscripcionesPrioridad);

        // 2/3 SORTEO: GENERAL
        $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesEmpadronados($curso);
        $this->logger->addInfo(
            sprintf('SORTEO GENERAL: %d candidatos', sizeof($preinscripciones))
        );
        $this->ejecutarSorteo(sizeof($preinscripciones), $preinscripciones);

        // 3/3 SORTEO: NO EMPADRONADOS
        $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesNoEmpadronados($curso);
        $this->logger->addInfo(
            sprintf('SORTEO NO EMPADRONADOS: %d candidatos', sizeof($preinscripciones))
        );
        $this->ejecutarSorteo(sizeof($preinscripciones), $preinscripciones);

    }

    public function asignarPlazas(Curso $curso)
    {
        $plazasAAsignar = $curso->getNumeroPlazas();
        $preinscripcionesOrdenadas = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));

        $asignadas = 0;
        foreach($preinscripcionesOrdenadas as $preinscripcion) {
            if($asignadas < $plazasAAsignar) {
                $preinscripcion->setEstado(PreinscripcionEnCurso::ESTADO_PLAZA);
                $asignadas++;
            } else
                $preinscripcion->setEstado(PreinscripcionEnCurso::ESTADO_RESERVA);

            $this->em->persist($preinscripcion);
        }

        $this->em->flush();
    }

    private function ejecutarSorteo($numeroPlazas, $preinscripciones)
    {
        $plazasAsignadas = 0;
        for($i = 0; $i < $numeroPlazas; $i++) {
            $asignada = false;
            while(!$asignada AND ($plazasAsignadas < sizeof($preinscripciones))) {   // Mientras no haya sido asignada la plaza en juego y el número de plazas que han sido asigandos sea inferior al total de preinscripciones con prioridad
                $numeroAleatorio = mt_rand(0, sizeof($preinscripciones)-1);
                $preinscipcion = $preinscripciones[$numeroAleatorio];
                if($preinscipcion->getNumeroLista() == PreinscripcionEnCurso::SIN_PLAZA) {
                    $preinscipcion->setNumeroLista($this->posicionLista);
                    $this->em->persist($preinscipcion);
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
            if($curso->getEntraEnSorteo())
                $this->inicializarListado($curso);

        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin de la incialización a las %s', $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->setGeneracionDeListas(null);
        $this->em->flush();
    }

    private function inicializarListado(Curso $curso)
    {
        $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripciones($curso);
        foreach($preinscripciones as $preinscripcion) {
            $preinscripcion->setNumeroLista(PreinscripcionEnCurso::SIN_PLAZA);
            $preinscripcion->setEstado(PreinscripcionEnCurso::ESTADO_PREINSCRITO);
            $this->em->persist($preinscripcion);
        }
        $this->em->flush();
    }

    private function generarSemilla()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }
}