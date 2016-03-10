<?php

namespace AppBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

class AddCursoAcademicoSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA    => 'preSetData',
            FormEvents::PRE_SUBMIT      => 'preSubmit'
        );
    }

    private function addCursoAcademicoForm($form, $cursoAcademico)
    {
        $form->add($this->factory->createNamed('cursoAcademico', 'entity', $cursoAcademico, array(
            'class'         => 'AppBundle:CursoAcademico',
            'mapped'        => false,
            'empty_value'   => '',
            'auto_initialize'=> false,
            'query_builder' => function (EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('cursoAcademico');

                return $qb;
            }
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $cursoAcademico = ($data->getCurso()) ? $data->getCurso()->getCursoAcademico() : null ;
        $this->addCursoAcademicoForm($form, $cursoAcademico);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $cursoAcademico = array_key_exists('cursoAcademico', $data) ? $data['cursoAcademico'] : null;
        $this->addCursoAcademicoForm($form, $cursoAcademico);
    }
}
