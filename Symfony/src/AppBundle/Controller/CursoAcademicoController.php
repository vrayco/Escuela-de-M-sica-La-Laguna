<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Form\CursoAcademicoType;

/**
 * CursoAcademico controller.
 *
 * @Route("/cursoacademico")
 */
class CursoAcademicoController extends Controller
{
    /**
     * Lists all CursoAcademico entities.
     *
     * @Route("/", name="cursoacademico_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cursoAcademicos = $em->getRepository('AppBundle:CursoAcademico')->findBy(array(), array('id' => 'DESC'));

        return $this->render('cursoacademico/index.html.twig', array(
            'cursoAcademicos' => $cursoAcademicos,
        ));
    }

    /**
     * Creates a new CursoAcademico entity.
     *
     * @Route("/new", name="cursoacademico_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cursoAcademico = new CursoAcademico();
        $form = $this->createForm('AppBundle\Form\CursoAcademicoType', $cursoAcademico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if($cursoAcademico->getEnCurso())
                $em->getRepository('AppBundle:CursoAcademico')->clearEnCurso($cursoAcademico);

            $em->persist($cursoAcademico);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado el curso académico'
            );

            return $this->redirectToRoute('cursoacademico_show', array('id' => $cursoAcademico->getId()));
        }

        return $this->render('cursoacademico/new.html.twig', array(
            'cursoAcademico' => $cursoAcademico,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CursoAcademico entity.
     *
     * @Route("/{id}", name="cursoacademico_show")
     * @Method("GET")
     */
    public function showAction(CursoAcademico $cursoAcademico)
    {
        $deleteForm = $this->createDeleteForm($cursoAcademico);

        return $this->render('cursoacademico/show.html.twig', array(
            'cursoAcademico' => $cursoAcademico,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CursoAcademico entity.
     *
     * @Route("/{id}/en-curso", name="cursoacademico_encurso")
     * @Method({"GET", "POST"})
     */
    public function enCursoAction(Request $request, CursoAcademico $cursoAcademico)
    {
        $editForm = $this->createForm('AppBundle\Form\CursoAcademicoType', $cursoAcademico);
        $editForm->remove('nombre');
        $editForm->remove('fechaInicio');
        $editForm->remove('fechaFin');
        $editForm->remove('prefijoExpediente');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if($cursoAcademico->getEnCurso())
                $em->getRepository('AppBundle:CursoAcademico')->clearEnCurso($cursoAcademico);

            $em->persist($cursoAcademico);
            $em->flush();

            if($cursoAcademico->getEnCurso()) {
                $this->addFlash(
                    'success',
                    'Se ha establecido el curso académico como en curso'
                );
            }

            return $this->redirectToRoute('cursoacademico_show', array('id' => $cursoAcademico->getId()));
        }

        return $this->render('cursoacademico/en_curso.html.twig', array(
            'cursoAcademico' => $cursoAcademico,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing CursoAcademico entity.
     *
     * @Route("/{id}/edit", name="cursoacademico_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CursoAcademico $cursoAcademico)
    {
        $deleteForm = $this->createDeleteForm($cursoAcademico);
        $editForm = $this->createForm('AppBundle\Form\CursoAcademicoType', $cursoAcademico);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if($cursoAcademico->getEnCurso())
                $em->getRepository('AppBundle:CursoAcademico')->clearEnCurso($cursoAcademico);

            $em->persist($cursoAcademico);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado el curso académico'
            );

            return $this->redirectToRoute('cursoacademico_show', array('id' => $cursoAcademico->getId()));
        }

        return $this->render('cursoacademico/edit.html.twig', array(
            'cursoAcademico' => $cursoAcademico,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CursoAcademico entity.
     *
     * @Route("/{id}", name="cursoacademico_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CursoAcademico $cursoAcademico)
    {
        $form = $this->createDeleteForm($cursoAcademico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cursoAcademico);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado el curso académico'
            );
        }

        return $this->redirectToRoute('cursoacademico_index');
    }

    /**
     * Creates a form to delete a CursoAcademico entity.
     *
     * @param CursoAcademico $cursoAcademico The CursoAcademico entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CursoAcademico $cursoAcademico)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cursoacademico_delete', array('id' => $cursoAcademico->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
