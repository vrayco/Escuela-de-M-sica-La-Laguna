<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Disciplina;
use AppBundle\Form\DisciplinaType;

/**
 * Disciplina controller.
 *
 * @Route("/disciplina")
 */
class DisciplinaController extends Controller
{
    /**
     * Lists all Disciplina entities.
     *
     * @Route("/", name="disciplina_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $disciplinas = $em->getRepository('AppBundle:Disciplina')->findBy(array(),array('nombre' => 'ASC'));

        return $this->render('disciplina/index.html.twig', array(
            'disciplinas' => $disciplinas,
        ));
    }

    /**
     * Creates a new Disciplina entity.
     *
     * @Route("/new", name="disciplina_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $disciplina = new Disciplina();
        $form = $this->createForm('AppBundle\Form\DisciplinaType', $disciplina);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disciplina);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado la disciplina'
            );


            return $this->redirectToRoute('disciplina_show', array('id' => $disciplina->getId()));
        }

        return $this->render('disciplina/new.html.twig', array(
            'disciplina' => $disciplina,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Disciplina entity.
     *
     * @Route("/{id}", name="disciplina_show")
     * @Method("GET")
     */
    public function showAction(Disciplina $disciplina)
    {
        $deleteForm = $this->createDeleteForm($disciplina);

        return $this->render('disciplina/show.html.twig', array(
            'disciplina' => $disciplina,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Disciplina entity.
     *
     * @Route("/{id}/edit", name="disciplina_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Disciplina $disciplina)
    {
        $deleteForm = $this->createDeleteForm($disciplina);
        $editForm = $this->createForm('AppBundle\Form\DisciplinaType', $disciplina);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disciplina);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado la disciplina'
            );


            return $this->redirectToRoute('disciplina_show', array('id' => $disciplina->getId()));
        }

        return $this->render('disciplina/edit.html.twig', array(
            'disciplina' => $disciplina,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Disciplina entity.
     *
     * @Route("/{id}", name="disciplina_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Disciplina $disciplina)
    {
        $form = $this->createDeleteForm($disciplina);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disciplina);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado la disciplina'
            );

        }

        return $this->redirectToRoute('disciplina_index');
    }

    /**
     * Creates a form to delete a Disciplina entity.
     *
     * @param Disciplina $disciplina The Disciplina entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Disciplina $disciplina)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disciplina_delete', array('id' => $disciplina->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
