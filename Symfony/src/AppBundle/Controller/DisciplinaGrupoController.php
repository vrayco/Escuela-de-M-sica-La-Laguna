<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DisciplinaGrupo;
use AppBundle\Form\DisciplinaGrupoType;

/**
 * DisciplinaGrupo controller.
 *
 * @Route("/disciplinagrupo")
 */
class DisciplinaGrupoController extends Controller
{
    /**
     * Lists all DisciplinaGrupo entities.
     *
     * @Route("/", name="disciplinagrupo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $disciplinaGrupos = $em->getRepository('AppBundle:DisciplinaGrupo')->findBy(array(),array('nombre'=>'ASC'));

        return $this->render('disciplinagrupo/index.html.twig', array(
            'disciplinaGrupos' => $disciplinaGrupos,
        ));
    }

    /**
     * Creates a new DisciplinaGrupo entity.
     *
     * @Route("/new", name="disciplinagrupo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $disciplinaGrupo = new DisciplinaGrupo();
        $form = $this->createForm('AppBundle\Form\DisciplinaGrupoType', $disciplinaGrupo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disciplinaGrupo);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado el grupo disciplina'
            );

            return $this->redirectToRoute('disciplinagrupo_show', array('id' => $disciplinaGrupo->getId()));
        }

        return $this->render('disciplinagrupo/new.html.twig', array(
            'disciplinaGrupo' => $disciplinaGrupo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DisciplinaGrupo entity.
     *
     * @Route("/{id}", name="disciplinagrupo_show")
     * @Method("GET")
     */
    public function showAction(DisciplinaGrupo $disciplinaGrupo)
    {
        $deleteForm = $this->createDeleteForm($disciplinaGrupo);

        return $this->render('disciplinagrupo/show.html.twig', array(
            'disciplinaGrupo' => $disciplinaGrupo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DisciplinaGrupo entity.
     *
     * @Route("/{id}/edit", name="disciplinagrupo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DisciplinaGrupo $disciplinaGrupo)
    {
        $deleteForm = $this->createDeleteForm($disciplinaGrupo);
        $editForm = $this->createForm('AppBundle\Form\DisciplinaGrupoType', $disciplinaGrupo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disciplinaGrupo);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado el grupo disciplina'
            );

            return $this->redirectToRoute('disciplinagrupo_show', array('id' => $disciplinaGrupo->getId()));
        }

        return $this->render('disciplinagrupo/edit.html.twig', array(
            'disciplinaGrupo' => $disciplinaGrupo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DisciplinaGrupo entity.
     *
     * @Route("/{id}", name="disciplinagrupo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DisciplinaGrupo $disciplinaGrupo)
    {
        $form = $this->createDeleteForm($disciplinaGrupo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disciplinaGrupo);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado el grupo disciplina'
            );
        }

        return $this->redirectToRoute('disciplinagrupo_index');
    }

    /**
     * Creates a form to delete a DisciplinaGrupo entity.
     *
     * @param DisciplinaGrupo $disciplinaGrupo The DisciplinaGrupo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisciplinaGrupo $disciplinaGrupo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disciplinagrupo_delete', array('id' => $disciplinaGrupo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
