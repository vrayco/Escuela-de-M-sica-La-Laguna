<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Alumno;
use AppBundle\Form\AlumnoType;

/**
 * Alumno controller.
 *
 * @Route("/alumno")
 */
class AlumnoController extends Controller
{
    /**
     * Lists all Alumno entities.
     *
     * @Route("/", name="alumno_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filter['expediente'] = $request->query->get('expediente');
        $filter['dni'] = $request->query->get('dni');
        $filter['nombre'] = $request->query->get('nombre');
        $filter['apellidos'] = $request->query->get('apellidos');
        $filter['fecha_nacimiento'] = $this->get('utils.fechas')->getDateTimeToStr($request->query->get('fecha_nacimiento'));

        $alumnos = $em->getRepository('AppBundle:Alumno')->getAlumnos($filter);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $alumnos, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('alumno/index.html.twig', array(
            'alumnos' => $pagination,
            'filter'  => $filter,
        ));
    }

    /**
     * Creates a new Alumno entity.
     *
     * @Route("/new", name="alumno_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $alumno = new Alumno();

        // Si se viene desde el acceso directo en show curso
        if($request->query->get('preinscripcion')) {
            $preinscripcionEnCurso = $em->getRepository('AppBundle:PreinscripcionEnCurso')->find($request->query->get('preinscripcion'));
            if ($preinscripcionEnCurso) {
                $preinscripcion = $preinscripcionEnCurso->getPreinscripcion();
                $alumno->setDni($preinscripcion->getDni());
                $alumno->setNombre($preinscripcion->getNombre());
                $alumno->setApellidos($preinscripcion->getApellidos());
                $alumno->setFechaNacimiento($preinscripcion->getFechaNacimiento());
                $alumno->setTelefonoMovil($preinscripcion->getTelefonoMovil());
                $now = new \DateTime('now');
                $alumno->setAnoIngreso($now->format('Y'));
            }
        }

        $form = $this->createForm('AppBundle\Form\AlumnoType', $alumno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $alumno = $this->get('utils.expediente')->setExpediente($alumno);

            $em->persist($alumno);
            $em->flush();

            $this->addFlash(
                'success',
                'El alumno se ha creado correctamente'
            );

            return $this->redirectToRoute('alumno_show', array('id' => $alumno->getId()));
        }

        return $this->render('alumno/new.html.twig', array(
            'alumno' => $alumno,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Alumno entity.
     *
     * @Route("/{id}", name="alumno_show")
     * @Method("GET")
     */
    public function showAction(Alumno $alumno)
    {
        $deleteForm = $this->createDeleteForm($alumno);

        return $this->render('alumno/show.html.twig', array(
            'alumno' => $alumno,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Alumno entity.
     *
     * @Route("/{id}/edit", name="alumno_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Alumno $alumno)
    {
        $deleteForm = $this->createDeleteForm($alumno);
        $editForm = $this->createForm('AppBundle\Form\AlumnoType', $alumno);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alumno);
            $em->flush();

            $this->addFlash(
                'success',
                'El alumno se ha actualizado correctamente'
            );

            return $this->redirectToRoute('alumno_show', array('id' => $alumno->getId()));
        }

        return $this->render('alumno/edit.html.twig', array(
            'alumno' => $alumno,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Alumno entity.
     *
     * @Route("/{id}", name="alumno_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Alumno $alumno)
    {
        $form = $this->createDeleteForm($alumno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($alumno);
            $em->flush();

            $this->addFlash(
                'success',
                'El alumno se ha eliminado'
            );
        }

        return $this->redirectToRoute('alumno_index');
    }

    /**
     * Creates a form to delete a Alumno entity.
     *
     * @param Alumno $alumno The Alumno entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Alumno $alumno)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('alumno_delete', array('id' => $alumno->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
