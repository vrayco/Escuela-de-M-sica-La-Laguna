<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCursoData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $entity = new CursoAcademico();
        $entity->setNombre("2015-2016");
        $entity->setFechaInicio(new \DateTime('2015-09-01'));
        $entity->setFechaFin(new \DateTime('2016-06-30'));
        $entity->setPrefijoExpediente('A');
        $entity->setEnCurso(true);
        $manager->persist($entity);
        $this->addReference('CURSO-ACADEMICO-1', $entity);

        $entity = new CursoAcademico();
        $entity->setNombre("2016-2017");
        $entity->setFechaInicio(new \DateTime('2016-09-01'));
        $entity->setFechaFin(new \DateTime('2017-06-30'));
        $entity->setPrefijoExpediente('B');
        $manager->persist($entity);
        $this->addReference('CURSO-ACADEMICO-2', $entity);

        $disciplinas = $manager->getRepository('AppBundle:Disciplina')->findAll();

        foreach($disciplinas as $disciplina)
        {
            $entity = new Curso();
            $entity->setDisciplina($disciplina);
            $entity->setNumeroPlazas(rand(5,20));
            $entity->setNumeroPlazasPrioritarias(intdiv($entity->getNumeroPlazas()*10,100));  // 10% del numero de plazas
            $entity->setCursoAcademico($this->getReference('CURSO-ACADEMICO-1'));
            $manager->persist($entity);
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
        return 3;
    }
}