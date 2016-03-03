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
use Symfony\Component\Validator\Constraints\IsTrue;

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

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $preinscripciones, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('preinscripcion/index.html.twig', array(
            'preinscripciones' => $pagination,
            'filter' => $filter
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
            $cursoAcademico = $p->getCurso()->getCursoAcademico();

        if(!$cursoAcademico->getGeneracionDeListas())
            $deleteForm = $this->createDeleteForm($preinscripcion);

        return $this->render('preinscripcion/show.html.twig', array(
            'preinscripcion'    => $preinscripcion,
            'delete_form'       => $deleteForm != null ? $deleteForm->createView() : null,
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

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render(':preinscripcion:generar_listas.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/sorteo/inicializar", name="preinscripcion_inicializarsorteo")
     * @Method("GET")
     */
    public function inicializarSorteoListasAction(Request $request)
    {
        $cursoAcademico = $this->get('utils.curso')->getCursoActual();

        $this->get('utils.sorteoplazas')->inicializarSorteo($cursoAcademico);

        $this->addFlash(
            'info',
            sprintf('Sorteo eliminado')
        );


        return $this->redirect($this->generateUrl('homepage'));

    }

    /**
     * @Route("/plaza/{id}/aceptar/o/rechazar", name="preinscripcion_plazaaceptarorechazar")
     * @Method({"GET", "POST"})
     */
    public function cambiarEstadoPlazaActionAction(Request $request, PreinscripcionEnCurso $preinscripcionEnCurso)
    {
        if($preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_PLAZA AND $preinscripcionEnCurso->getEstado() != PreinscripcionEnCurso::ESTADO_RECHAZADA)
        {
            $this->addFlash(
                'info',
                sprintf('No se puede cambiar la preinscripción a el estado: %s', $preinscripcionEnCurso->getEstado())
            );

            return $this->redirect($this->generateUrl('curso_listados'));
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\PreinscripcionEnCursoEstadoType', $preinscripcionEnCurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_ACEPTADA) {
                $preinscripciones = $preinscripcionEnCurso->getPreinscripcion()->getPreinscripcionEnCursos();
                foreach($preinscripciones as $p) {
                    if($preinscripcionEnCurso != $p)
                        $p->setEstado(PreinscripcionEnCurso::ESTADO_RECHAZADA);
                }

                foreach($preinscripciones as $p) {
                    $this->get('utils.curso')->actualizarAsignacionPlazas($p->getCurso());
                }

            } else if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_RECHAZADA) {
                $this->get('utils.curso')->actualizarAsignacionPlazas($preinscripcionEnCurso->getCurso());
            } else if($preinscripcionEnCurso->getEstado() == PreinscripcionEnCurso::ESTADO_PLAZA) {
                $preinscripciones = $preinscripcionEnCurso->getPreinscripcion()->getPreinscripcionEnCursos();
                foreach($preinscripciones as $p) {
                    if($preinscripcionEnCurso != $p)
                        $p->setEstado(PreinscripcionEnCurso::ESTADO_PLAZA);
                }

                foreach($preinscripciones as $p) {
                    $this->get('utils.curso')->actualizarAsignacionPlazas($p->getCurso());
                }
            }

            return $this->redirect($this->generateUrl('curso_show', array('id' => $preinscripcionEnCurso->getCurso()->getId())).'?tab=preinscripciones');
        }

        return $this->render(':preinscripcion:aceptar_o_rechazar_plaza.html.twig', array(
            'form' => $form->createView()
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
