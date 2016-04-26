<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrematriculaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alumno', EntityType::class, array(
                'required' => true,
                'label' => 'Alumno',
                'choice_label' => function ($alumno) {
                    return $alumno->getExpediente().' - '. $alumno->__toString();
                },
                'class' => 'AppBundle:Alumno',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.apellidos', 'ASC');
                },
            ))
            ->add('prematriculaEnCursos', CollectionType::class, array(
                'entry_type' => PrematriculaEnCursoType::class,
            ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Prematricula'
        ));
    }
}
