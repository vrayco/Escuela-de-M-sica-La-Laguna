<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Alumno;
use AppBundle\Entity\Tutor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAlumnosData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    const NUM_ENTITIES = 100;

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
        for($i=0; $i<self::NUM_ENTITIES; $i++)
        {
            $entity = new Alumno();
            $entity->setExpediente("A".rand(0,99999));
            $entity->setNombre("Alumno".$i);
            $entity->setApellidos("Apellido1 Apellido2");
            $entity->setDni(rand(40000000,79999999));
            //$entity->setAñoIngreso();
            $entity->setLocalidad("Localidad");
            $entity->setDireccion("Dirección");
            $entity->setCodigoPostal(38250);
            $entity->setTelefonofijo(rand(922000000,922999999));
            $entity->setTelefonoMovil(rand(600000000,660000000));
            //$entity->setEmail();
            $entity->setFechaNacimiento(new \DateTime('now -'.rand(4,18).'year'));

            if(rand(0,1)) {
                $entity2 = new Tutor();
                $entity2->setNombre("Nombre");
                $entity2->setApellidos("Apellido1 Apellido2");
                $entity2->setDni(rand(40000000,79999999));
                $entity2->setAlumno($entity);
                $entity->setTutor($entity2);
            }

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
        return 1;
    }
}