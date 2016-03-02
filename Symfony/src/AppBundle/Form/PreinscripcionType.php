<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscripcionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dni')
            ->add('nombre')
            ->add('apellidos')
            ->add('fechaNacimiento', DateType::class, array(
                'widget'    => 'single_text',
                'format'    => 'dd/MM/yyyy'
            ))
            ->add('telefonoMovil')
            ->add('prioridad')
            ->add('empadronado')
            ->add('preinscripcionEnCursos', CollectionType::class, array(
                'entry_type' => PreinscripcionEnCursoType::class,
            ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Preinscripcion'
        ));
    }
}
