<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoAcademicoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('fechaInicio', DateType::class, array(
                'widget'    => 'single_text',
                'format'    => 'dd/MM/yyyy'
            ))
            ->add('fechaFin', DateType::class, array(
                'widget'    => 'single_text',
                'format'    => 'dd/MM/yyyy'
            ))
            ->add('prefijoExpediente')
            ->add('enCurso')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CursoAcademico'
        ));
    }
}
