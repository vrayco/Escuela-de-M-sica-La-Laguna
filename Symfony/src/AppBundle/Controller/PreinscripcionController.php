<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PreinscripcionEnCurso;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Preinscripcion;
use AppBundle\Form\PreinscripcionType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * Preinscripcion controller.
 *
 * @Route("/preinscripcion")
 */
class PreinscripcionController extends Controller
{

    const LISTADO_PRIORIDAD         = "LISTADO_PRIORIDAD";
    const LISTADO_EMPADRONADOS      = "LISTADO_EMPADRONADOS";
    const LISTADO_NO_EMPADRONADOS   = "LISTADO_NO_EMPADRONADOS";

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

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $preinscripciones, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('preinscripcion/index.html.twig', array(
            'preinscripciones'  => $pagination,
            'filter'            => $filter,
            'cursoAcademico'    => $cursoAcademico
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

        for($i = 0; $i < Preinscripcion::NUM_CURSOS; $i++) {
            $preinscripcionEncurso = new PreinscripcionEnCurso();
            $preinscripcionEncurso->setPreinscripcion($preinscripcion);
            $preinscripcion->addPreinscripcionEnCurso($preinscripcionEncurso);
        }

        $form = $this->createForm('AppBundle\Form\PreinscripcionType', $preinscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Elimino las preinscripcionEnCurso nulas
            $preinscripcionesEncursos = $preinscripcion->getPreinscripcionEnCursos();
            foreach ($preinscripcionesEncursos as $p)
                if (!$p->getCurso())
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
        $deleteForm = null;
        $preinscripcionEnCursos = $preinscripcion->getPreinscripcionEnCursos();
        foreach($preinscripcionEnCursos as $p)
            $cursoAcademico = $p->getCurso()->getCursoAcademico();   // <--- NO ENTIENDO PORQUE HACERLO ASI

        if(!$cursoAcademico->getGeneracionDeListas())
            $deleteForm = $this->createDeleteForm($preinscripcion);

        return $this->render('preinscripcion/show.html.twig', array(
            'preinscripcion'    => $preinscripcion,
            'delete_form'       => $deleteForm != null ? $deleteForm->createView() : null,
        ));
    }

    /**
     * Displays a form to edit an existing Preinscripcion entity.
     *
     * @Route("/{id}/edit", name="preinscripcion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Preinscripcion $preinscripcion)
    {
        if($preinscripcion->getCursoAcademico()->getGeneracionDeListas())
            throw new NotFoundHttpException('No se puede editar la preinscripción despúes del sorteo.');

        for($i = count($preinscripcion->getPreinscripcionEnCursos()); $i < Preinscripcion::NUM_CURSOS; $i++) {
            $preinscripcionEnCurso = new PreinscripcionEnCurso();
            $preinscripcionEnCurso->setPreinscripcion($preinscripcion);
            $preinscripcion->addPreinscripcionEnCurso($preinscripcionEnCurso);
        }

        $editForm = $this->createForm('AppBundle\Form\PreinscripcionType', $preinscripcion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Elimino las preinscripcionEnCurso nulas
            $preinscripcionesEncursos = $preinscripcion->getPreinscripcionEnCursos();
            foreach ($preinscripcionesEncursos as $p)
                if (!$p->getCurso()) {
                    $preinscripcion->removePreinscripcionEnCurso($p);
                    $em->remove($p);
                }

            $em->persist($preinscripcion);
            $em->flush();

            $this->addFlash(
                'success',
                'Se ha actualizado la preinscripcion'
            );

            return $this->redirectToRoute('preinscripcion_show', array('id' => $preinscripcion->getId()));
        }

        return $this->render(':preinscripcion:edit.html.twig', array(
            'preinscripcion' => $preinscripcion,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * @Route("/sortear/plazas", name="preinscripcion_sortearplazas")
     * @Method({"GET","POST"})
     */
    public function sortearPlazasListasAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        if ($cursoAcademico->getGeneracionDeListas()) {
            $this->addFlash(
                'info',
                sprintf('El sorteo de plazas fue celebrado el %s', $cursoAcademico->getGeneracionDeListas()->format('d/m/Y H:i:s'))
            );

            return $this->redirect($this->generateUrl('homepage'));
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

            $this->get('utils.sorteoplazas')->celebrarSorteo($cursoAcademico);
            $this->get('utils.curso')->refreshCursoAcademico();

            $this->addFlash(
                'info',
                sprintf('Sorteo realizado')
            );

            return $this->redirect($this->generateUrl('listado', array('slug' => $cursoAcademico)));
        }

        return $this->render(':preinscripcion:generar_listas.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/sorteo/eliminar", name="preinscripcion_eliminarsorteo")
     * @Method({"GET","POST"})
     */
    public function eliminarSorteoListasAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        if (!$cursoAcademico->getGeneracionDeListas()) {
            $this->addFlash(
                'info',
                sprintf('El sorteo de plazas no ha sido celebrado.')
            );

            return $this->redirect($this->generateUrl('preinscripcion_index'));
        }

        $defaultData = array('message' => 'Sortear plazas');
        $form = $this->createFormBuilder($defaultData)
            ->add('sortear', CheckboxType::class, array(
                'label' => 'Sí, estoy seguro de eliminar el sorteo de plazas pre-inscripciones',
                'constraints' => new IsTrue(array('message' => 'Tienes que confirmar que deseas generar las listas')),
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->get('utils.sorteoplazas')->inicializarSorteo($cursoAcademico);

            $this->addFlash(
                'info',
                sprintf('Sorteo eliminado')
            );

            return $this->redirect($this->generateUrl('preinscripcion_index'));
        }

        return $this->render(':preinscripcion:eliminar_listas.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/plaza/{id}/aceptar/o/rechazar", name="preinscripcion_plazaaceptarorechazar")
     * @Method({"GET", "POST"})
     */
    public function cambiarEstadoPlazaActionAction(Request $request, PreinscripcionEnCurso $preinscripcionEnCurso)
    {
//        // Si tiene plaza o la tiene rechazada
//        if($preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_PLAZA AND $preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_RECHAZADA)
//        {
//            $this->addFlash(
//                'info',
//                sprintf('No se puede cambiar la preinscripción a el estado: %s', $preinscripcionEnCurso->getEstado())
//            );
//
//            return $this->redirect($this->generateUrl('curso_listados'));
//        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\PreinscripcionEnCursoEstadoType', $preinscripcionEnCurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->get('utils.curso')->actualizarAsignacionPlazas($preinscripcionEnCurso->getCurso());
//
//            if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_ACEPTADA) {
//                $preinscripciones = $preinscripcionEnCurso->getPreinscripcion()->getPreinscripcionEnCursos();
//                foreach($preinscripciones as $p)
//                    if($preinscripcionEnCurso != $p)
//                        if($preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_PLAZA OR $preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_RESERVA)
//                            $p->setEstado(PreinscripcionEnCurso::ESTADO_RECHAZADA);
//
//                foreach($preinscripciones as $p)
//                    $this->get('utils.curso')->actualizarAsignacionPlazas($p->getCurso());
//
//            } else if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_RECHAZADA) {
//                $this->get('utils.curso')->actualizarAsignacionPlazas($preinscripcionEnCurso->getCurso());
//            } else if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_PLAZA) {
//                $preinscripciones = $preinscripcionEnCurso->getPreinscripcion()->getPreinscripcionEnCursos();
//                foreach($preinscripciones as $p)
//                    if($preinscripcionEnCurso != $p)
//                        $p->setEstado(PreinscripcionEnCurso::ESTADO_PLAZA);
//
//                foreach($preinscripciones as $p)
//                    $this->get('utils.curso')->actualizarAsignacionPlazas($p->getCurso());
//            }

            return $this->redirect($this->generateUrl('curso_show', array('id' => $preinscripcionEnCurso->getCurso()->getId())).'?tab=preinscripciones');
        }

        return $this->render(':preinscripcion:aceptar_o_rechazar_plaza.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/descargar/listado", name="preinscripcion_descargarlistado")
     * @Method("GET")
     */
    public function descargarListado(Request $request)
    {
        $tipo = $request->query->get('tipo-listado');

        $cursoAcademico = $this->get('utils.curso')->getCursoActual();
        if(!$tipo)
            return $this->get('utils.listados')->generarListadoPreinscripciones($cursoAcademico);
        elseif($tipo == self::LISTADO_PRIORIDAD)
            return $this->get('utils.listados')->generarListadoPreinscripciones($cursoAcademico, self::LISTADO_PRIORIDAD);
        elseif($tipo == self::LISTADO_EMPADRONADOS)
            return $this->get('utils.listados')->generarListadoPreinscripciones($cursoAcademico, self::LISTADO_EMPADRONADOS);
        elseif($tipo == self::LISTADO_NO_EMPADRONADOS)
            return $this->get('utils.listados')->generarListadoPreinscripciones($cursoAcademico, self::LISTADO_NO_EMPADRONADOS);
        else
            throw new NotFoundHttpException('Tipo de listado desconocido');
    }

    /**
     * Deletes a Preinscripcion entity.
     *
     * @Route("/{id}", name="preinscripcion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Preinscripcion $preinscripcion)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        if ($cursoAcademico->getGeneracionDeListas()) {
            $this->addFlash(
                'info',
                sprintf('No se permite eliminar la pre-inscripción debido a que el sorteo de plazas fue celebrado el %s', $cursoAcademico->getGeneracionDeListas()->format('d/m/Y H:i:s'))
            );

            return $this->redirect($this->generateUrl('homepage'));
        }

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
