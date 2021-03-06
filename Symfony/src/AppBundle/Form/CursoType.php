<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disciplina', EntityType::class, array(
                'class' => 'AppBundle:Disciplina',
                'choice_label' => function ($disciplina) {
                    return $disciplina->getNombre().' ('. $disciplina->getDisciplinaGrupo()->getNombre().')';
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.nombre', 'ASC');
                },))
            ->add('entraEnSorteoPrematricula', null, array('label' => 'Entra en el sorteo de pre-matrícula'))
            ->add('numeroPlazasPrematricula', null, array('label' => 'Número plazas a sortear en pre-matrícula'))
            ->add('entraEnSorteo', null, array('label' => 'Entra en el sorteo de pre-inscripciones'))
            ->add('numeroPlazas', null, array('label' => 'Número plazas a sortear en pre-inscripciones'))
            ->add('numeroPlazasPrioritarias', null, array('label' => 'Número plazas prioritarias a sortear en pre-inscripciones'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Curso'
        ));
    }
}
