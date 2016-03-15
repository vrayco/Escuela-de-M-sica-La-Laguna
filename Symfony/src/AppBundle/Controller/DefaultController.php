<?php

namespace AppBundle\Controller;


use AppBundle\Entity\CursoAcademico;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $em = $this->getDoctrine()->getManager();
        $totalMatriculas = $em->getRepository('AppBundle:Matricula')->getTotal($cursoAcademico);
        $totalPreinscripciones = $em->getRepository('AppBundle:Preinscripcion')->getTotal($cursoAcademico);
        $totalPlazas = $em->getRepository('AppBundle:Curso')->getTotalPlazas($cursoAcademico);

        return $this->render('default/index.html.twig', array(
            'cursoAcademico'    => $cursoAcademico,
            'totalMatriculas'   => $totalMatriculas,
            'totalPreinscripciones' => $totalPreinscripciones,
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
            'cursos'    => $cursos,
            'listados'  => $listados
        ));
    }

}
