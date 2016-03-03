<?php

namespace AppBundle\Form;

use AppBundle\Entity\CursoAcademico;
use AppBundle\Utils\CursoService;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\AppBundle\Entity\PreinscripcionEnCurso;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscripcionEnCursoEstadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estado',ChoiceType::class, array(
                'choices'  => array(
                    PreinscripcionEnCurso::ESTADO_ACEPTADA  => 'Aceptar plaza',
                    PreinscripcionEnCurso::ESTADO_RECHAZADA => 'Rechazar plaza'
                )))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PreinscripcionEnCurso'
        ));
    }
}
