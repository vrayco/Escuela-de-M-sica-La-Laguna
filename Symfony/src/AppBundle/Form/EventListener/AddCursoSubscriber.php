<?php

namespace AppBundle\Form\EventListener;

use AppBundle\Entity\CursoAcademico;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

class AddCursoSubscriber implements EventSubscriberInterface
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

    private function addCursoForm($form, $curso, $cursoAcademico)
    {
        $form->add($this->factory->createNamed('curso','entity', $curso, array(
            'class'         => 'AppBundle:Curso',
            'label'         => 'Disciplina',
            'empty_value'   => '',
            'auto_initialize'=> false,
            'query_builder' => function (EntityRepository $repository) use ($cursoAcademico) {
                $qb = $repository->createQueryBuilder('curso')
                    ->innerJoin('curso.cursoAcademico', 'cursoAcademico');
                if($cursoAcademico instanceof CursoAcademico){
                    $qb->where('curso.cursoAcademico = :cursoAcademico')
                        ->setParameter('cursoAcademico', $cursoAcademico);
                }elseif(is_numeric($cursoAcademico)){
                    $qb->where('cursoAcademico.id = :cursoAcademico')
                        ->setParameter('cursoAcademico', $cursoAcademico);
                }else{
                    $qb->where('cursoAcademico.nombre = :cursoAcademico')
                        ->setParameter('cursoAcademico', null);
                }

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

        $curso = ($data->getCurso()) ? $data->getCurso() : null ;
        $cursoAcademico = ($curso) ? $curso->getCursoAcademico() : null ;
        $this->addCursoForm($form, $curso, $cursoAcademico);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $curso = array_key_exists('curso', $data) ? $data['curso'] : null;
        $cursoAcademico = array_key_exists('cursoAcademico', $data) ? $data['cursoAcademico'] : null;
        $this->addCursoForm($form, $curso, $cursoAcademico);
    }
}