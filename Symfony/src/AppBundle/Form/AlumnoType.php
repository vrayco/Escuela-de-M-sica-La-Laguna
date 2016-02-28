<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expediente')
            ->add('nombre')
            ->add('apellidos')
            ->add('dni')
            ->add('añoIngreso')
            ->add('localidad')
            ->add('direccion')
            ->add('codigoPostal')
            ->add('telefonofijo')
            ->add('telefonoMovil')
            ->add('email')
            ->add('fechaNacimiento', 'datetime')
            ->add('observaciones')
            ->add('createAt', 'datetime')
            ->add('tutor')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Alumno'
        ));
    }
}
