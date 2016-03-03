<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Matricula;
use AppBundle\Form\MatriculaType;

/**
 * Matricula controller.
 *
 * @Route("/matricula")
 */
class MatriculaController extends Controller
{
    /**
     * Lists all Matricula entities.
     *
     * @Route("/", name="matricula_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        $em = $this->getDoctrine()->getManager();

        $filter['identificador'] = $request->query->get('identificador');
        $filter['curso'] = $request->query->get('curso');
        $filter['expedienteNumero'] = $request->query->get('expediente-numero');
        $filter['expedienteLetra'] = $request->query->get('expediente-prefijo');
        $filter['dni'] = $request->query->get('dni');
        $filter['nombre'] = $request->query->get('nombre');
        $filter['apellidos'] = $request->query->get('apellidos');
        $filter['fecha_nacimiento'] = $this->get('utils.fechas')->getDateTimeToStr($request->query->get('fecha_nacimiento'));

        $matriculas = $em->getRepository('AppBundle:Matricula')->getMatriculas($cursoAcademico, $filter);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $matriculas, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        $cursos = $em->getRepository('AppBundle:Curso')->getCursos($cursoAcademico);

        return $this->render('matricula/index.html.twig', array(
            'matriculas'    => $pagination,
            'filter'        => $filter,
            'cursos'        => $cursos
        ));
    }

    /**
     * Creates a new Matricula entity.
     *
     * @Route("/new", name="matricula_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $matricula = new Matricula();
        $matricula->setPrefijo($cursoAcademico->getPrefijoExpediente());

        $form = $this->createForm('AppBundle\Form\MatriculaType', $matricula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($matricula);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado la matrícula'
            );

            return $this->redirectToRoute('matricula_show', array('id' => $matricula->getId()));
        }

        return $this->render('matricula/new.html.twig', array(
            'matricula' => $matricula,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Matricula entity.
     *
     * @Route("/{id}", name="matricula_show")
     * @Method("GET")
     */
    public function showAction(Matricula $matricula)
    {
        $deleteForm = $this->createDeleteForm($matricula);

        return $this->render('matricula/show.html.twig', array(
            'matricula' => $matricula,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Matricula entity.
     *
     * @Route("/{id}/edit", name="matricula_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Matricula $matricula)
    {
        $editForm = $this->createForm('AppBundle\Form\MatriculaType', $matricula);
        $editForm->remove('alumno');
        $editForm->remove('curso');
        $editForm->add('estado');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($matricula);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado el estado de la matrícula'
            );

            return $this->redirectToRoute('matricula_show', array('id' => $matricula->getId()));
        }

        return $this->render('matricula/edit.html.twig', array(
            'matricula' => $matricula,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Matricula entity.
     *
     * @Route("/{id}", name="matricula_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Matricula $matricula)
    {
        $form = $this->createDeleteForm($matricula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($matricula);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado la matrícula'
            );
        }

        return $this->redirectToRoute('matricula_index');
    }

    /**
     * Creates a form to delete a Matricula entity.
     *
     * @param Matricula $matricula The Matricula entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Matricula $matricula)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('matricula_delete', array('id' => $matricula->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
