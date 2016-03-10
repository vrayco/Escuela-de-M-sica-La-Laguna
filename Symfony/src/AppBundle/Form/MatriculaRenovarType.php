<?php

namespace AppBundle\Form;

use AppBundle\Entity\Matricula;
use AppBundle\Form\EventListener\AddCursoAcademicoSubscriber;
use AppBundle\Form\EventListener\AddCursoSubscriber;
use AppBundle\Utils\CursoService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatriculaRenovarType extends AbstractType
{
    private $matriculaVieja = null;

    public function __construct(Matricula $matriculaVieja)
    {
        $this->matriculaVieja = $matriculaVieja;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $cursoAcademicoSubscriber = new AddCursoAcademicoSubscriber($factory);
        $cursoSubscriber = new AddCursoSubscriber($factory);
        $builder
            ->addEventSubscriber($cursoAcademicoSubscriber)
            ->addEventSubscriber($cursoSubscriber)
            ->add('fraccionaPago')
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                // Relaciono el tutor con el alumno
                $matricula = $event->getForm()->getData();
                $form = $event->getForm();

                if($matricula->getCurso() != null) {
                    if ($matricula->getCurso()->getCursoAcademico() == $this->matriculaVieja->getCurso()->getCursoAcademico())
                        $form->get('cursoAcademico')->addError(new FormError(
                            sprintf('Estás intentando renovar una matricula con origen y destino el mismo curso académico: %s', $matricula->getCurso()->getCursoAcademico())
                        ));
                }
            })
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Matricula'
        ));
    }
}
