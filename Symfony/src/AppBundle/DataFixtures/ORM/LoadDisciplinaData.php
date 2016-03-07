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
    const DG_MUSICA_MOVIMIENTO              = "Música y Movimiento";
    const DG_PRACTICA_INSTRUMETAL           = "Práctica Instrumental";
    const DG_PRATICA_INSTRUMENTAL_MODERNO   = "Práctica Instrumental Moderno";
    const DG_BANDA_JUVENIL                  = "Banda Juvenil";
    const DG_CORO_JUVENIL                   = "Coro Juvenil";
    const DG_CORO_ADULTO                    = "Coro Adulto";
    const DG_ORQUESTA_JUVENIL               = "Orquesta Juvenil";
    const DG_TALLER_FOLKLORE                = "Taller Folklore";

    const D_MUSICA_MOVIMIENTO_1_1   = "Música y Movimiento 1º - 1º Curso";
    const D_MUSICA_MOVIMIENTO_1_2   = "Música y Movimiento 1º - 2º Curso";
    const D_MUSICA_MOVIMIENTO_2_1   = "Música y Movimiento 2º - 1º Curso";
    const D_MUSICA_MOVIMIENTO_2_2   = "Música y Movimiento 2º - 2º Curso";
    const D_CLARINETE               = "Clarinete";
    const D_FLAUTA                  = "Flauta";
    const D_SAXOFON                 = "Saxofón";
    const D_TROMPETA                = "Trompeta";
    const D_PERCUSION               = "Percusión";
    const D_CELLO                   = "Cello";
    const D_GUITARRA_CLASICA        = "Guitarra Clásica";
    const D_VIOLIN                  = "Violín";
    const D_PIANO                   = "Piano";
    const D_GUITARRA_MODERNA        = "Guitarra Moderna";
    const D_BAJO_MODERNO            = "Bajo Moderno";
    const D_BATERIA                 = "Batería";

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
        $entity->setNombre(self::DG_MUSICA_MOVIMIENTO);
        $entity->setIncompatibleConOtro(true);
        $entity->setMaximoInscripciones(1);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_PRACTICA_INSTRUMETAL);
        $entity->setMaximoInscripciones(3);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_PRATICA_INSTRUMENTAL_MODERNO);
        $entity->setMaximoInscripciones(3);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_BANDA_JUVENIL);
        $entity->setMaximoInscripciones(3);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-BANDA-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_CORO_JUVENIL);
        $entity->setMaximoInscripciones(1);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-CORO-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_CORO_ADULTO);
        $entity->setMaximoInscripciones(1);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-CORO-ADULTO', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_ORQUESTA_JUVENIL);
        $entity->setMaximoInscripciones(3);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-ORQUESTA-JUVENIL', $entity);

        $entity = new DisciplinaGrupo();
        $entity->setNombre(self::DG_TALLER_FOLKLORE);
        $entity->setMaximoInscripciones(3);
        $manager->persist($entity);
        $this->addReference('DISCIPLINA-GRUPO-TALLER-FOLKLORE', $entity);

        // DISCIPLINAS
        $entity = new Disciplina();
        $entity->setNombre(self::D_MUSICA_MOVIMIENTO_1_1);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(4);
        $entity->setEdadMaxima(4);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_MUSICA_MOVIMIENTO_1_2);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(5);
        $entity->setEdadMaxima(5);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_MUSICA_MOVIMIENTO_2_1);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(6);
        $entity->setEdadMaxima(6);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_MUSICA_MOVIMIENTO_2_2);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-MUSICA-Y-MOVIMIENTO'));
        $entity->setEdadMinima(7);
        $entity->setEdadMaxima(7);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_CLARINETE);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_FLAUTA);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_SAXOFON);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_TROMPETA);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_PERCUSION);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_CELLO);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_GUITARRA_CLASICA);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_VIOLIN);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_PIANO);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL'));
        $entity->setEdadMinima(8);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_GUITARRA_MODERNA);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO'));
        $entity->setEdadMinima(14);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_BAJO_MODERNO);
        $entity->setDisciplinaGrupo($this->getReference('DISCIPLINA-GRUPO-PRACTICA-INSTRUMENTAL-MODERNO'));
        $entity->setEdadMinima(14);
        $manager->persist($entity);

        $entity = new Disciplina();
        $entity->setNombre(self::D_BATERIA);
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