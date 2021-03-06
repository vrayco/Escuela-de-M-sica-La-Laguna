<?php

namespace AppBundle\Controller;


use AppBundle\Entity\CursoAcademico;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $em = $this->getDoctrine()->getManager();
        $totalPrematriculas = $em->getRepository('AppBundle:Prematricula')->getTotal($cursoAcademico);
        $totalPreinscripciones = $em->getRepository('AppBundle:Preinscripcion')->getTotal($cursoAcademico);
        $totalMatriculas = $em->getRepository('AppBundle:Matricula')->getTotal($cursoAcademico);
        $totalPlazas = $em->getRepository('AppBundle:Curso')->getTotalPlazas($cursoAcademico);

        return $this->render('default/index.html.twig', array(
            'cursoAcademico'        => $cursoAcademico,
            'totalPrematriculas'    => $totalPrematriculas,
            'totalPreinscripciones' => $totalPreinscripciones,
            'totalMatriculas'       => $totalMatriculas,
            'totalPlazas'           => $totalPlazas
        ));
    }

    /**
     * @Route("/sorteo/{slug}/resultado", name="listado")
     * @Method("GET")
     */
    public function listadoAction(CursoAcademico $cursoAcademico)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        $em = $this->getDoctrine()->getManager();
        $cursos = $em->getRepository('AppBundle:Curso')->getCursosEntraEnSorteo($cursoAcademico);

        $listados = array();
        foreach($cursos as $curso)
            $listados[$curso->getId()] = $em->getRepository('AppBundle:PreinscripcionEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));

        return $this->render(':default:listado.html.twig', array(
            'cursos'            => $cursos,
            'listados'          => $listados,
            'cursoAcademico'    => $cursoAcademico
        ));
    }

    /**
     * @Route("/sorteo/pre-matriculas/{slug}/resultado", name="listado_prematriculas")
     * @Method("GET")
     */
    public function listadoPrematriculasAction(CursoAcademico $cursoAcademico)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        $em = $this->getDoctrine()->getManager();
        $cursos = $em->getRepository('AppBundle:Curso')->getCursosEntraEnSorteoPrematricula($cursoAcademico);

        $listados = array();
        foreach($cursos as $curso) {
                $listados[$curso->getId()] = $em->getRepository('AppBundle:PrematriculaEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));
        }

        return $this->render(':default:listado_prematriculas.html.twig', array(
            'cursos'            => $cursos,
            'listados'          => $listados,
            'cursoAcademico'    => $cursoAcademico
        ));
    }

    /**
     * @Route("/sorteo/log/descargar", name="sorteo_descargar_log")
     * @Method("GET")
     */
    public function descargarLogAction(Request $request)
    {
        $path = __DIR__.'/../../../app/logs/';
        $filename = 'escuelademusica_sorteo.log';
        $filepath = $path.$filename;

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
        $response->headers->set('Content-length', filesize($filepath));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($filepath));
        
        return $response;
    }

    private function _mime_content_type($filename) {
        $result = new \finfo();

        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }

        return false;
    }
}
