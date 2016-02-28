<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Disciplina;
use AppBundle\Entity\DisciplinaGrupo;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadDisciplinaData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        // DISCIPLINA GRUPOS
        $entity = new DisciplinaGrupo();
        $entity->setNombre("Música y Movimiento");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Práctica Instrumental");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Práctica Instrumental Moderno");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Banda Juvenil");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-BANDA-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Coro Juvenil");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-CORO-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Coro Adulto");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-CORO-ADULTO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Orquesta Juvenil");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-ORQUESTA-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre("Taller Folklore");
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-TALLER-FOLKLORE', $entity);

        // DISCIPLINAS
        $entity = new Disciplina();
        $entity->setNombre("Música y Movimiento 1º - 1º Curso");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(4);
        $entity->setEdadMaxima(5);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Música y Movimiento 1º - 2º Curso");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(5);
        $entity->setEdadMaxima(6);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Música y Movimiento 2º - 1º Curso");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(6);
        $entity->setEdadMaxima(7);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Música y Movimiento 2º - 2º Curso");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(7);
        $entity->setEdadMaxima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Clarinete");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Flauta");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Saxofón");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Trompeta");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Percusión");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Cello");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Guitarra Clásica");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Violín");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Piano");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Guitarra Moderna");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO'));
        $entity->setEdadMinima(14);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Bajo Moderno");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO'));
        $entity->setEdadMinima(14);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre("Batería");
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO'));
        $entity->setEdadMinima(14);
        $manager->persist($entity);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}