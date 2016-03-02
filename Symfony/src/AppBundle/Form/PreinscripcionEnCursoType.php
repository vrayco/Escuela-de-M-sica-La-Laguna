<?php

namespace AppBundle\Form;

use AppBundle\Entity\CursoAcademico;
use AppBundle\Utils\CursoService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscripcionEnCursoType extends AbstractType
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
//            ->add('curso')
            ->add('curso', EntityType::class, array(
                'required' => false,
                'label' => false,
                'class' => 'AppBundle:Curso',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.disciplina','d')
                        ->innerJoin('c.cursoAcademico','cu')
                        ->where('cu = :cursoAcademico')
                        ->setParameter('cursoAcademico', $this->cursoService->getCursoActual())
                        ->orderBy('d.nombre', 'ASC');
                },
            ))
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
