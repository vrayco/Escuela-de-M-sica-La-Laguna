<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PrematriculaEnCurso
 *
 * @ORM\Table(name="prematricula_en_curso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrematriculaEnCursoRepository")
 * @UniqueEntity({"curso","prematricula"},
 *    errorPath="curso",
 *    message="Disciplina repetida en la prematricula.")
 */
class PrematriculaEnCurso
{
    const ESTADO_PREMATRICULADO = "PREMATRICULADO";
    const ESTADO_PLAZA          = "PLAZA";
    const ESTADO_DESCARTADA     = "DESCARTADA";
    const ESTADO_SIN_PLAZA      = "SIN PLAZA";
    const ESTADO_MATRICULADO    = "MATRICULADO";

    const SIN_PLAZA = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="preferencia", type="integer")
     */
    private $preferencia;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=32)
     */
    private $estado;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroLista", type="integer", nullable=true)
     */
    private $numeroLista;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Prematricula", inversedBy="prematriculaEnCursos")
     * @ORM\JoinColumn(name="prematricula_id", referencedColumnName="id", nullable=false)
     */
    private $prematricula;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Curso", inversedBy="prematriculas")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id", nullable=false)
     */
    private $curso;

    public function __construct()
    {
        $this->preferencia = 0;
        $this->estado = self::ESTADO_PREMATRICULADO;
        $this->numeroLista = self::SIN_PLAZA;
    }

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
     * Set preferencia
     *
     * @param integer $preferencia
     * @return PrematriculaEnCurso
     */
    public function setPreferencia($preferencia)
    {
        $this->preferencia = $preferencia;

        return $this;
    }

    /**
     * Get preferencia
     *
     * @return integer 
     */
    public function getPreferencia()
    {
        return $this->preferencia;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return PrematriculaEnCurso
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set prematricula
     *
     * @param \AppBundle\Entity\Prematricula $prematricula
     * @return PrematriculaEnCurso
     */
    public function setPrematricula(\AppBundle\Entity\Prematricula $prematricula)
    {
        $this->prematricula = $prematricula;

        return $this;
    }

    /**
     * Get prematricula
     *
     * @return \AppBundle\Entity\Prematricula 
     */
    public function getPrematricula()
    {
        return $this->prematricula;
    }

    /**
     * Set curso
     *
     * @param \AppBundle\Entity\Curso $curso
     * @return PrematriculaEnCurso
     */
    public function setCurso(\AppBundle\Entity\Curso $curso)
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * Get curso
     *
     * @return \AppBundle\Entity\Curso 
     */
    public function getCurso()
    {
        return $this->curso;
    }

    /**
     * Set numeroLista
     *
     * @param integer $numeroLista
     * @return PrematriculaEnCurso
     */
    public function setNumeroLista($numeroLista)
    {
        $this->numeroLista = $numeroLista;

        return $this;
    }

    /**
     * Get numeroLista
     *
     * @return integer 
     */
    public function getNumeroLista()
    {
        return $this->numeroLista;
    }
}
