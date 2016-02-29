<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CursoAcademico;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/ui")
 * @Security("has_role('ROLE_USER')")
 */
class UIController extends Controller
{
    /**
     * @Route("/curso-academico/selector", name="ui_curso_academico_selector")
     * @Method("GET")
     */
    public function getSelectorCursoAcademicoAction()
    {
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();
        $cursosAcademicos = $em->getRepository('AppBundle:CursoAcademico')->findAll();

        $cursoAcademicoActual = $session->get(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR);

        if(!$cursoAcademicoActual and sizeof($cursosAcademicos) > 0) {
            $cursoAcademicoActual = $cursosAcademicos[0];
            $session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $cursoAcademicoActual);
        }

        return $this->render(':ui/curso_academico:selector.html.twig', array(
            'cursosAcademicos'      => $cursosAcademicos,
            'cursoAcademicoActual'  => $cursoAcademicoActual
        ));
    }

    /**
     * @Route("/curso-academico/set", name="ui_curso_academico_set")
     *
     * @Method("GET")
     */
    public function setCursoAcademico(Request $request)
    {
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();

        $id = $request->query->get('id-curso-academico');
        $cursoAcademico = $em->getRepository('AppBundle:CursoAcademico')->find($id);

        if(!$cursoAcademico) {
            $response->setData(array(
                    'code'  => Response::HTTP_NOT_FOUND
                ));
        } else {
            $session->set(CursoAcademico::CURSO_ACADEMICO_SESSION_VAR, $cursoAcademico);

            $response->setData(array(
                'code'          => Response::HTTP_OK,
                'cursoAcademico' => $cursoAcademico->getNombre()
            ));

            $session->getFlashBag()->add('info','Has seleccionado el curso: '. $cursoAcademico->getNombre());
        }

        return $response;
    }
}
