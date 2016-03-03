<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\Preinscripcion;
use AppBundle\Entity\PreinscripcionEnCurso;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPreinscripcionData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    const NUM_ENTITIES = 1000;
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

        $cursos = $manager->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoEnCurso));

        for($j=0; $j<self::NUM_ENTITIES; $j++) {
            $entity = new Preinscripcion();
            $entity->setPrefijo($cursoEnCurso->getPrefijoExpediente());
            $entity->setNombre("Alumno".$j);
            $entity->setApellidos("Apellido Apellido");
            $entity->setFechaNacimiento(new \DateTime('now -'.rand(4,18).'year'));
            $entity->setEmpadronado(rand(0,1));
            $entity->setPrioridad(rand(0,1));

            $numCursos = rand(1,3);

            // Para evitar que se repitan
            $ok = false;
            $numerosAleatorios = array();
            while(!$ok) {
                $numeroAleatorio = rand(0,sizeof($cursos)-1);
                if(!in_array($numeroAleatorio,$numerosAleatorios))
                    $numerosAleatorios[sizeof($numerosAleatorios)] = $numeroAleatorio;

                if(sizeof($numerosAleatorios)==$numCursos)
                    $ok =true;
            }

            for($i=0; $i<$numCursos; $i++)
            {
                $entity2 = new PreinscripcionEnCurso();
                $entity2->setPreinscripcion($entity);
                $entity2->setCurso($cursos[$numerosAleatorios[$i]]);
                $entity->addPreinscripcionEnCurso($entity2);
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
        return 4;
    }
}