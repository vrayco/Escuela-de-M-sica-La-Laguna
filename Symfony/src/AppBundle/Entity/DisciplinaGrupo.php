<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DisciplinaGrupo
 *
 * @ORM\Table(name="disciplina_grupo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DisciplinaGrupoRepository")
 */
class DisciplinaGrupo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Disciplina", mappedBy="disciplinaGrupo", cascade={"persist","remove"})
     */
    private $disciplinas;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return DisciplinaGrupo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->disciplinas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add disciplinas
     *
     * @param \AppBundle\Entity\Disciplina $disciplinas
     * @return DisciplinaGrupo
     */
    public function addDisciplina(\AppBundle\Entity\Disciplina $disciplinas)
    {
        $this->disciplinas[] = $disciplinas;

        return $this;
    }

    /**
     * Remove disciplinas
     *
     * @param \AppBundle\Entity\Disciplina $disciplinas
     */
    public function removeDisciplina(\AppBundle\Entity\Disciplina $disciplinas)
    {
        $this->disciplinas->removeElement($disciplinas);
    }

    /**
     * Get disciplinas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDisciplinas()
    {
        return $this->disciplinas;
    }
}
