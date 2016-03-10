<?php

namespace AppBundle\Form;

use AppBundle\Utils\CursoService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatriculaType extends AbstractType
{
    private $cursoService = null;

    public function __construct(CursoService $cursoService)
    {
        $this->cursoService = $cursoService;
    }

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
            ->add('curso', EntityType::class, array(
                'required' => true,
                'label' => 'Disciplina',
                'class' => 'AppBundle:Curso',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.disciplina','d')
                        ->innerJoin('c.cursoAcademico', 'cu')
                        ->where('cu = :cursoAcademico')
                        ->setParameter('cursoAcademico', $this->cursoService->getCursoActual())
                        ->orderBy('d.nombre', 'ASC');
                },
            ))
            ->add('fraccionaPago')
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
