<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Matricula;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMatriculaData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $alumnos = $manager->getRepository('AppBundle:Alumno')->findAll();
        $cursos = $manager->getRepository('AppBundle:Curso')->findAll();

        foreach($alumnos as $alumno)
        {
            if(rand(0,5))
            {
                $entity = new Matricula();
                $entity->setAlumno($alumno);
                $entity->setCurso($cursos[rand(0,sizeof($cursos)-1)]);
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