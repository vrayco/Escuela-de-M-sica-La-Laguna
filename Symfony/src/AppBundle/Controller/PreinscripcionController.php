<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PreinscripcionEnCurso;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Preinscripcion;
use AppBundle\Form\PreinscripcionType;

/**
 * Preinscripcion controller.
 *
 * @Route("/preinscripcion")
 */
class PreinscripcionController extends Controller
{
    /**
     * Lists all Preinscripcion entities.
     *
     * @Route("/", name="preinscripcion_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        $em = $this->getDoctrine()->getManager();

        $filter['identificador'] = $request->query->get('identificador');
        $filter['dni'] = $request->query->get('dni');
        $filter['nombre'] = $request->query->get('nombre');
        $filter['apellidos'] = $request->query->get('apellidos');
        $filter['fecha_nacimiento'] = $this->get('utils.fechas')->getDateTimeToStr($request->query->get('fecha_nacimiento'));

        $preinscripciones = $em->getRepository('AppBundle:Preinscripcion')->getPreinscripciones($cursoAcademico, $filter);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $preinscripciones, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('preinscripcion/index.html.twig', array(
            'preinscripciones'  => $pagination,
            'filter'            => $filter
        ));
    }

    /**
     * Creates a new Preinscripcion entity.
     *
     * @Route("/new", name="preinscripcion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $preinscripcion = new Preinscripcion();
        $preinscripcion->setPrefijo($cursoAcademico->getPrefijoExpediente());

        $preinscripcionEnCurso1 = new PreinscripcionEnCurso();
        $preinscripcionEnCurso2 = new PreinscripcionEnCurso();
        $preinscripcionEnCurso3 = new PreinscripcionEnCurso();

        $preinscripcionEnCurso1->setPreinscripcion($preinscripcion);
        $preinscripcionEnCurso2->setPreinscripcion($preinscripcion);
        $preinscripcionEnCurso3->setPreinscripcion($preinscripcion);

        $preinscripcion->addPreinscripcionEnCurso($preinscripcionEnCurso1);
        $preinscripcion->addPreinscripcionEnCurso($preinscripcionEnCurso2);
        $preinscripcion->addPreinscripcionEnCurso($preinscripcionEnCurso3);

        $form = $this->createForm('AppBundle\Form\PreinscripcionType', $preinscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Elimino las preinscripcionEnCurso nulas
            $preinscripcionesEncursos = $preinscripcion->getPreinscripcionEnCursos();
            foreach($preinscripcionesEncursos as $p)
                if(!$p->getCurso())
                    $preinscripcion->removePreinscripcionEnCurso($p);


            $em = $this->getDoctrine()->getManager();
            $em->persist($preinscripcion);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado la inscripción'
            );

            return $this->redirectToRoute('preinscripcion_show', array('id' => $preinscripcion->getId()));
        }

        return $this->render('preinscripcion/new.html.twig', array(
            'preinscripcion' => $preinscripcion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Preinscripcion entity.
     *
     * @Route("/{id}", name="preinscripcion_show")
     * @Method("GET")
     */
    public function showAction(Preinscripcion $preinscripcion)
    {
        $deleteForm = $this->createDeleteForm($preinscripcion);

        return $this->render('preinscripcion/show.html.twig', array(
            'preinscripcion' => $preinscripcion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Preinscripcion entity.
     *
     * @Route("/{id}", name="preinscripcion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Preinscripcion $preinscripcion)
    {
        $form = $this->createDeleteForm($preinscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($preinscripcion);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado la inscripción'
            );
        }

        return $this->redirectToRoute('preinscripcion_index');
    }

    /**
     * Creates a form to delete a Preinscripcion entity.
     *
     * @param Preinscripcion $preinscripcion The Preinscripcion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Preinscripcion $preinscripcion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('preinscripcion_delete', array('id' => $preinscripcion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
