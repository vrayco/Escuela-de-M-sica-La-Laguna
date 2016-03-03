<?php

namespace AppBundle\Controller;

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

}
