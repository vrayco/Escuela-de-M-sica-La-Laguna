<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CursoAcademico;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Curso;
use AppBundle\Form\CursoType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Curso controller.
 *
 * @Route("/curso")
 */
class CursoController extends Controller
{

    /**
     * @Route("/selects", name="cursos_selects")
     */
    public function cursosAction(Request $request)
    {
        $cursoAcademicoId = $request->request->get('curso_academico_id');
        $em = $this->getDoctrine()->getManager();
        $cities = $em->getRepository('AppBundle:Curso')->findByCursoAcademicoId($cursoAcademicoId);
        return new JsonResponse($cities);
    }

    /**
     * Lists all Curso entities.
     *
     * @Route("/", name="curso_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $cursos = $em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico), array());

        return $this->render('curso/index.html.twig', array(
            'cursos'            => $cursos
        ));
    }

    /**
     * Creates a new Curso entity.
     *
     * @Route("/new", name="curso_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $cursoAcademico = $em->getRepository('AppBundle:CursoAcademico')->find($cursoAcademico->getId());

        $curso = new Curso();
        $curso->setCursoAcademico($cursoAcademico);
        $form = $this->createForm('AppBundle\Form\CursoType', $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($curso);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha creado la especialidad'
            );

            return $this->redirectToRoute('curso_show', array('id' => $curso->getId()));
        }

        return $this->render('curso/new.html.twig', array(
            'curso' => $curso,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Curso entity.
     *
     * @Route("/{id}", name="curso_show")
     * @Method("GET")
     */
    public function showAction(Request $request, Curso $curso)
    {
        $deleteForm = null;
        if(!$curso->getCursoAcademico()->getGeneracionDeListas())
            $deleteForm = $this->createDeleteForm($curso);

        $em = $this->getDoctrine()->getManager();
        $preinscripciones   = $em->getRepository('AppBundle:PreinscripcionEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));
        $matriculas         = $em->getRepository('AppBundle:Matricula')->findBy(array('curso' => $curso), array('id' => 'ASC'));
        $prematriculas      = $em->getRepository('AppBundle:PrematriculaEnCurso')->findBy(array('curso' => $curso), array('numeroLista' => 'ASC'));

        $tab = $request->query->get('tab');

        return $this->render('curso/show.html.twig', array(
            'curso'             => $curso,
            'preinscripciones'  => $preinscripciones,
            'matriculas'        => $matriculas,
            'prematriculas'     => $prematriculas,
            'tab'               => $tab,
            'delete_form'       => $deleteForm != null ? $deleteForm->createView() : null,
        ));
    }

    /**
     * Displays a form to edit an existing Curso entity.
     *
     * @Route("/{id}/edit", name="curso_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Curso $curso)
    {
        if($curso->getCursoAcademico()->getGeneracionDeListas())
            throw new NotFoundHttpException('No se puede editar el curso despúes del sorteo.');

        $deleteForm = $this->createDeleteForm($curso);
        $editForm = $this->createForm('AppBundle\Form\CursoType', $curso);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($curso);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado la especialidad'
            );

            return $this->redirectToRoute('curso_show', array('id' => $curso->getId()));
        }

        return $this->render('curso/edit.html.twig', array(
            'curso' => $curso,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/descargar/listado", name="curso_descargarlistado")
     * @Method("GET")
     */
    public function descargarListado()
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        return $this->get('utils.listados')->generarListadoCursos($cursoAcademico);
    }

    /**
     * Deletes a Curso entity.
     *
     * @Route("/{id}", name="curso_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Curso $curso)
    {
        $form = $this->createDeleteForm($curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($curso);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha eliminado la especialidad'
            );
        }

        return $this->redirectToRoute('curso_index');
    }

    /**
     * Creates a form to delete a Curso entity.
     *
     * @param Curso $curso The Curso entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Curso $curso)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('curso_delete', array('id' => $curso->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
