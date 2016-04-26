<?php

namespace AppBundle\Utils;


use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\PreinscripcionEnCurso;
use AppBundle\Entity\Prematricula;
use AppBundle\Entity\PrematriculaEnCurso;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;

class SorteoPlazasPrematriculaService
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

        $this->inicializarSorteo($cursoAcademico);

        $semilla = $this->generarSemilla();
        srand($semilla);

        $this->logger->addInfo(
            sprintf('Incorporamos la semilla %f al generador de números aleatorios.', $semilla)
        );

        $prematriculas = $this->em->getRepository('AppBundle:Prematricula')->getPrematriculas($cursoAcademico);
        $cursos = $this->generarArrayCursosYPlazas($cursoAcademico);

        $this->logger->addInfo(
            sprintf('Número total de especialidades: %d', count($cursos))
        );

        foreach ($cursos as $curso)
            $this->logger->addInfo(
                sprintf('Plazas en %s: %d', $curso['curso'], $curso['curso']->getNumeroPlazasPrematricula())
            );

        $this->logger->addInfo(
            sprintf('Número total de solicitantes: %d .', count($prematriculas))
        );

        $finSorteo = false;
        $contador = 0;
        $visitados = array();
        while(!$finSorteo) {
            $aleatorio = mt_rand(0, count($prematriculas)-1);
            if(!in_array($aleatorio, $visitados)) {
                $visitados[] = $aleatorio;
                $prematricula = $prematriculas[$aleatorio];

                $this->logger->addInfo(
                    sprintf('Seleccionado %s %s', $prematricula->getAlumno()->getExpediente(), $prematricula->getAlumno())
                );

                $plazaAsignada = false;
                for ($i = 1; $i <= Prematricula::NUM_CURSOS; $i++) {

                    $prematriculaEnCurso = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculaEnCurso($prematricula, $i);

                    if($prematriculaEnCurso) {
                        if($plazaAsignada) {
                            $prematriculaEnCurso->setEstado(PrematriculaEnCurso::ESTADO_DESCARTADA);
                            $this->logger->addInfo(
                                sprintf('Preferencia %d: %s (PLAZA DESCARTADA)', $i, $prematriculaEnCurso->getCurso())
                            );
                        }
                        else {
                            if ($cursos[$prematriculaEnCurso->getCurso()->getId()]['plazas_disponibles'] > 0) {
                                $prematriculaEnCurso->setEstado(PrematriculaEnCurso::ESTADO_PLAZA);
                                $plazaAsignada = true;
                                $cursos[$prematriculaEnCurso->getCurso()->getId()]['plazas_disponibles']--;

                                $this->logger->addInfo(
                                    sprintf('Preferencia %d: %s (PLAZA)', $i, $prematriculaEnCurso->getCurso())
                                );
                            } else {
                                $prematriculaEnCurso->setEstado(PrematriculaEnCurso::ESTADO_SIN_PLAZA);
                                $this->logger->addInfo(
                                    sprintf('Preferencia %d: %s (SIN PLAZA)', $i, $prematriculaEnCurso->getCurso())
                                );
                            }
                        }
                    }
                }

                $contador++;
                if ($contador == count($prematriculas))
                    $finSorteo = true;
            }
        }


        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin del %s a las %s', self::NOMBRE_SORTEO, $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->setPrematriculasGeneracionDeListas($ahora);
        $this->em->flush();
    }

    public function inicializarSorteo(CursoAcademico $cursoAcademico)
    {
        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Inicializo el %s a las %s', self::NOMBRE_SORTEO, $ahora->format('d/m/Y H:i:s'))
        );

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        foreach($cursos as $curso)
            if($curso->getEntraEnSorteoPrematricula())
                $this->inicializarListado($curso);

        $ahora = new \DateTime('now');
        $this->logger->addInfo(
            sprintf('Fin de la incialización a las %s', $ahora->format('d/m/Y H:i:s'))
        );

        $cursoAcademico->SetPrematriculasGeneracionDeListas(null);
        $this->em->flush();
    }

    private function inicializarListado(Curso $curso)
    {
        $prematriculas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculas($curso);
        foreach($prematriculas as $prematricula) {
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

    private function generarArrayCursosYPlazas(CursoAcademico $cursoAcademico) {
        $cursos = $this->em->getRepository('AppBundle:Curso')->getCursosEntraEnSorteo($cursoAcademico);
        $result = array();
        foreach ($cursos as $curso) {
            $result[$curso->getId()] = array(
                'curso'                 => $curso,
                'plazas_disponibles'    => $curso->getNumeroPlazasPrematricula()
            );
        }

        return $result;
    }

}