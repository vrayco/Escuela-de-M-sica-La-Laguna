<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Prematricula;
use AppBundle\Entity\PrematriculaEnCurso;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPreMatriculasData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $cursoEnCurso = $this->getReference('CURSO-ACADEMICO-1');
        $alumnos = $manager->getRepository('AppBundle:Alumno')->findAll();
        $cursos = $manager->getRepository('AppBundle:Curso')->findAll();

        foreach($alumnos as $alumno)
        {
            if(rand(0,3))
            {
                $entity = new Prematricula();
                $entity->setAlumno($alumno);
                $entity->setPrefijo($cursoEnCurso->getPrefijoExpediente());

                $prematricula1 = new PrematriculaEnCurso();
                $prematricula1->setPreferencia(1);
                $prematricula1->setCurso($cursos[rand(0,sizeof($cursos)-1)]);
                $prematricula1->setPrematricula($entity);

                $prematricula2 = new PrematriculaEnCurso();
                $prematricula2->setPreferencia(2);
                $prematricula2->setCurso($cursos[rand(0,sizeof($cursos)-1)]);
                $prematricula2->setPrematricula($entity);

                $prematricula3 = new PrematriculaEnCurso();
                $prematricula3->setPreferencia(3);
                $prematricula3->setCurso($cursos[rand(0,sizeof($cursos)-1)]);
                $prematricula3->setPrematricula($entity);

                $entity->addPrematriculaEnCurso($prematricula1);
                $entity->addPrematriculaEnCurso($prematricula2);
                $entity->addPrematriculaEnCurso($prematricula3);

                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}