<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('dni')
            ->add('nombre')
            ->add('apellidos')
            ->add('fechaNacimiento', DateType::class, array(
                'widget'    => 'single_text',
                'format'    => 'dd/MM/yyyy'
            ))
            ->add('direccion')
            ->add('codigoPostal')
            ->add('localidad')
            ->add('telefonoFijo')
            ->add('telefonoMovil')
            ->add('email')
            ->add('anoIngreso',null,array('label' => 'Año de ingreso'))
            ->add('observaciones', 'textarea', array(
                'required'=>false,
                'attr'  => array('class' => 'materialize-textarea')
            ))
            ->add('tutor', new TutorType(), array(
                'required'  => false
            ))
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                // Relaciono el tutor con el alumno
                $alumno = $event->getForm()->getData();

                if($alumno->getTutor())
                    $alumno->getTutor()->setAlumno($alumno);
            })
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
