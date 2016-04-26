<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PrematriculaEnCurso;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Prematricula;
use AppBundle\Form\PrematriculaType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * Prematricula controller.
 *
 * @Route("/prematricula")
 */
class PrematriculaController extends Controller
{
    /**
     * Lists all Prematricula entities.
     *
     * @Route("/", name="prematricula_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        $em = $this->getDoctrine()->getManager();

        $prematriculas = $em->getRepository('AppBundle:Prematricula')->getPrematriculas($cursoAcademico);
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $prematriculas, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );
        
        return $this->render('prematricula/index.html.twig', array(
            'prematriculas' => $pagination,
            'cursoAcademico'    => $cursoAcademico
        ));
    }

    /**
     * Creates a new Prematricula entity.
     *
     * @Route("/new", name="prematricula_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $prematricula = new Prematricula();
        $prematricula->setPrefijo($cursoAcademico->getPrefijoExpediente());

        for($i = 0; $i < Prematricula::NUM_CURSOS; $i++) {
            $prematriculaEnCurso = new PrematriculaEnCurso();
            $prematriculaEnCurso->setPrematricula($prematricula);
            $prematriculaEnCurso->setPreferencia($i+1);
            $prematricula->addPrematriculaEnCurso($prematriculaEnCurso);
        }

        $form = $this->createForm('AppBundle\Form\PrematriculaType', $prematricula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Elimino las prematriculasEnCurso nulas
            $prematriculaEnCursos = $prematricula->getPrematriculaEnCursos();
            foreach ($prematriculaEnCursos as $p)
                if (!$p->getCurso())
                    $prematricula->removePrematriculaEnCurso($p);

            $em = $this->getDoctrine()->getManager();
            $em->persist($prematricula);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado la pre-matrícula'
            );

            return $this->redirectToRoute('prematricula_show', array('id' => $prematricula->getId()));
        }

        return $this->render('prematricula/new.html.twig', array(
            'prematricula' => $prematricula,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Prematricula entity.
     *
     * @Route("/{id}", name="prematricula_show")
     * @Method("GET")
     */
    public function showAction(Prematricula $prematricula)
    {
        $deleteForm = $this->createDeleteForm($prematricula);

        return $this->render('prematricula/show.html.twig', array(
            'prematricula' => $prematricula,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Prematricula entity.
     *
     * @Route("/{id}/edit", name="prematricula_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Prematricula $prematricula)
    {
        if($prematricula->getCursoAcademico()->getPrematriculasGeneracionDeListas())
            throw new NotFoundHttpException('No se puede editar la prematricula despúes del sorteo.');

        for($i = count($prematricula->getPrematriculaEnCursos()); $i < Prematricula::NUM_CURSOS; $i++) {
            $prematriculaEnCurso = new PrematriculaEnCurso();
            $prematriculaEnCurso->setPrematricula($prematricula);
            $prematriculaEnCurso->setPreferencia($i+1);
            $prematricula->addPrematriculaEnCurso($prematriculaEnCurso);
        }
        
        $editForm = $this->createForm('AppBundle\Form\PrematriculaType', $prematricula);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // Elimino las prematriculasEnCurso nulas
            $prematriculaEnCursos = $prematricula->getPrematriculaEnCursos();
            foreach ($prematriculaEnCursos as $p)
                if (!$p->getCurso())
                    $prematricula->removePrematriculaEnCurso($p);

            $prematricula->setUpdatedAt(new \Datetime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($prematricula);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado la pre-matrícula'
            );


            return $this->redirectToRoute('prematricula_show', array('id' => $prematricula->getId()));
        }

        return $this->render('prematricula/edit.html.twig', array(
            'prematricula' => $prematricula,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * @Route("/descargar/listado", name="prematricula_descargarlistado")
     * @Method("GET")
     */
    public function descargarListado()
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        return $this->get('utils.listados')->generarListadoPrematriculas($cursoAcademico);
    }

    /**
     * @Route("/sortear/plazas", name="prematricula_sortearplazas")
     * @Method({"GET","POST"})
     */
    public function sortearPlazasListasAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        if ($cursoAcademico->getPrematriculasGeneracionDeListas()) {
            $this->addFlash(
                'info',
                sprintf('El sorteo de plazas fue celebrado el %s', $cursoAcademico->getPrematriculasGeneracionDeListas()->format('d/m/Y H:i:s'))
            );

            return $this->redirect($this->generateUrl('prematricula_index'));
        }

        $defaultData = array('message' => 'Sortear plazas');
        $form = $this->createFormBuilder($defaultData)
            ->add('sortear', CheckboxType::class, array(
                'label' => '¿Quieres realizar el sorteo de plazas?',
                'constraints' => new IsTrue(array('message' => 'Tienes que confirmar que deseas generar las listas')),
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->get('utils.sorteoplazasprematricula')->celebrarSorteo($cursoAcademico);
            $this->get('utils.curso')->refreshCursoAcademico();

            $this->addFlash(
                'info',
                sprintf('Sorteo realizado')
            );

            return $this->redirect($this->generateUrl('listado_prematriculas', array('slug' => $cursoAcademico)));
        }

        return $this->render(':prematricula:generar_listas.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/sorteo/eliminar", name="prematricula_eliminarsorteo")
     * @Method({"GET","POST"})
     */
    public function eliminarSorteoListasAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        if (!$cursoAcademico->getPrematriculasGeneracionDeListas()) {
            $this->addFlash(
                'info',
                sprintf('El sorteo de plazas no ha sido celebrado.')
            );

            return $this->redirect($this->generateUrl('prematricula_index'));
        }
        
        $defaultData = array('message' => 'Sortear plazas');
        $form = $this->createFormBuilder($defaultData)
            ->add('sortear', CheckboxType::class, array(
                'label' => 'Sí, estoy seguro de eliminar el sorteo de plazas pre-matrícula',
                'constraints' => new IsTrue(array('message' => 'Tienes que confirmar que deseas generar las listas')),
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->get('utils.sorteoplazasprematricula')->inicializarSorteo($cursoAcademico);

            $this->addFlash(
                'info',
                sprintf('Sorteo eliminado')
            );
            
            return $this->redirect($this->generateUrl('prematricula_index'));
        }

        return $this->render(':prematricula:eliminar_listas.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a Prematricula entity.
     *
     * @Route("/{id}", name="prematricula_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Prematricula $prematricula)
    {
        $form = $this->createDeleteForm($prematricula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($prematricula);
            $em->flush();
        }

        return $this->redirectToRoute('prematricula_index');
    }

    /**
     * Creates a form to delete a Prematricula entity.
     *
     * @param Prematricula $prematricula The Prematricula entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Prematricula $prematricula)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prematricula_delete', array('id' => $prematricula->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
