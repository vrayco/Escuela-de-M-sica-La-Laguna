<?php

namespace AppBundle\Utils;

use AppBundle\Entity\CursoAcademico;
use Doctrine\ORM\EntityManager;
use PHPExcel;
use Liuggio\ExcelBundle\Factory as PHPExeclFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ListadosService
{
    const creador = "ESCUELA MUNICIPAL DE MUSICA GUILLERMO GONZALEZ";

    private $em = null;
    private $phpExcel;

    public function __construct(EntityManager $em, PHPExeclFactory $phpExcel)
    {
        $this->em = $em;
        $this->phpExcel = $phpExcel;
    }

    /**
     * Genera el listado de pre-inscripciones (PRIORIDAD, EMPADRONADO, NO EMPADRONADO)
     */
    public function generarListadoPreinscripciones (CursoAcademico $cursoAcademico, $tipo = null) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator(self::creador)
            ->setLastModifiedBy(self::creador)
            ->setTitle(sprintf("Listado de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna preinscripciones curso%s", $this->slugify($cursoAcademico->getNombre())))
            ->setCategory("");

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $index = 0;
        foreach($cursos as $curso) {
            if($curso->getEntraEnSorteo()) {
                $phpExcelObject->createSheet();
                $phpExcelObject->setActiveSheetIndex($index);
                $sheet = $phpExcelObject->getActiveSheet();

                // TITULO Encabezado
                $titulo = $curso->getDisciplina().' - '.$curso->getDisciplina()->getDisciplinaGrupo();
                $encabezado = 'PREINSCRIPCIONES '.$curso->getDisciplina()->getDisciplinaGrupo().' - '.$curso->getDisciplina();

                $sheet->setCellValue('A1', $encabezado);
                $sheet->mergeCells('A1:E1');
                $sheet->setTitle(substr($titulo,0,30));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'DDDDDD')
                    ),
                    'font' => array(
                        'size'  => 14,
                        'bold'  => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:E1")->applyFromArray($style);

                // ENCABEZADO tabla
                $sheet->setCellValue('B2', 'Nombre del solicitante');
                $sheet->setCellValue('C2', 'Fecha Nacimiento');
                $sheet->setCellValue('D2', 'Prioridad');
                $sheet->setCellValue('E2', 'Empadronado');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B2B2B2')
                    ),
                    'font' => array(
                        'size'  => 12,
                        'bold'  => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A2:E2")->applyFromArray($style);

                // CONTENIDO DE LA TABLA
                $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesOrdenAlfabetico($curso, $tipo);

                $index2 = 3;
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'size'  => 10,
                        'bold'  => false
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFFF')
                    )
                );

                foreach($preinscripciones as $pre) {
                    $sheet->setCellValue('A'.$index2, $pre->getPreinscripcion()->getIdentificador());
                    $sheet->setCellValue('B'.$index2, $pre->getPreinscripcion()->getApellidos().', '.$pre->getPreinscripcion()->getNombre());
                    $sheet->setCellValue('C'.$index2, $pre->getPreinscripcion()->getFechaNacimiento()->format('d/m/Y'));
                    $sheet->setCellValue('D'.$index2, $pre->getPreinscripcion()->getPrioridad() ? 'Si' : 'No');
                    $sheet->setCellValue('E'.$index2, $pre->getPreinscripcion()->getEmpadronado() ? 'Si' : 'No');
                    $sheet->getStyle("A".$index2.":E".$index2)->applyFromArray($style);
                    $index2++;
                }

                // Ancho de la columas automatico
                foreach(range('A','E') as $columnID)
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);

                $index++;
            }
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $filename = "listado_preinscripciones_".$this->slugify($cursoAcademico->getNombre()).'.xls';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * Genera el listado resultado del sorteo para las pre-inscripciones
     */
    public function generarListadoCursos (CursoAcademico $cursoAcademico) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator(self::creador)
            ->setLastModifiedBy("Escula de Música La Laguna - App")
            ->setTitle(sprintf("Listado RESULTADO del SORTEO de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado RESULTADO del SORTEO de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado RESULTADO del SORTEO de pre-inscripciones %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna listado resultado sorteo pre-inscripcionescurso%s", $this->slugify($cursoAcademico->getNombre())))
            ->setCategory("");

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $index = 0;
        foreach($cursos as $curso) {
            if($curso->getEntraEnSorteo()) {
                $phpExcelObject->createSheet();
                $phpExcelObject->setActiveSheetIndex($index);
                $sheet = $phpExcelObject->getActiveSheet();

                // TITULO Encabezado
                $titulo = $curso->getDisciplina().' - '.$curso->getDisciplina()->getDisciplinaGrupo();
                $encabezado = 'LISTADO '.$curso->getDisciplina()->getDisciplinaGrupo().' - '.$curso->getDisciplina();

                $sheet->setCellValue('A1', $encabezado);
                $sheet->mergeCells('A1:G1');
                $sheet->setTitle(substr($titulo,0,30));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'DDDDDD')
                    ),
                    'font' => array(
                        'size'  => 14,
                        'bold'  => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:G1")->applyFromArray($style);

                // ENCABEZADO tabla
                $sheet->setCellValue('B2', 'Estado');
                $sheet->setCellValue('C2', 'Idenfificador');
                $sheet->setCellValue('D2', 'Nombre del alumno');
                $sheet->setCellValue('E2', 'Fecha Nacimiento');
                $sheet->setCellValue('F2', 'Prioridad');
                $sheet->setCellValue('G2', 'Empadronado');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B2B2B2')
                    ),
                    'font' => array(
                        'size'  => 12,
                        'bold'  => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A2:G2")->applyFromArray($style);

                // CONTENIDO DE LA TABLA
                $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesOrdenLista($curso);
                $index2 = 3;
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'size'  => 10,
                        'bold'  => false
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFFF')
                    )
                );

                foreach($preinscripciones as $pre) {
                    $sheet->setCellValue('A'.$index2, $pre->getNumeroLista());
                    $sheet->setCellValue('B'.$index2, $pre->getEstado());
                    $sheet->setCellValue('C'.$index2, $pre->getPreinscripcion()->getIdentificador());
                    $sheet->setCellValue('D'.$index2, $pre->getPreinscripcion()->getApellidos().', '.$pre->getPreinscripcion()->getNombre());
                    $sheet->setCellValue('E'.$index2, $pre->getPreinscripcion()->getFechaNacimiento()->format('d/m/Y'));
                    $sheet->setCellValue('F'.$index2, $pre->getPreinscripcion()->getPrioridad() ? 'Si' : 'No');
                    $sheet->setCellValue('G'.$index2, $pre->getPreinscripcion()->getEmpadronado() ? 'Si' : 'No');
                    $sheet->getStyle("A".$index2.":G".$index2)->applyFromArray($style);
                    $index2++;
                }

                // Ancho de la columas automatico
                foreach(range('A','G') as $columnID)
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);

                $index++;
            }
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $filename = "listado_".$this->slugify($cursoAcademico->getNombre()).'.xls';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /*
     * Genera el listado de pre-matrículas
     */
    public function generarListadoPrematriculas (CursoAcademico $cursoAcademico, $tipo = null) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator(self::creador)
            ->setLastModifiedBy(self::creador)
            ->setTitle(sprintf("Listado de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna prematriculados curso%s", $this->slugify($cursoAcademico->getNombre())))
            ->setCategory("");

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $index = 0;
        foreach($cursos as $curso) {
            $prematriculas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculasOrdenAlfabetico($curso);
            if (count($prematriculas) > 0) {
                $phpExcelObject->createSheet();
                $phpExcelObject->setActiveSheetIndex($index);
                $sheet = $phpExcelObject->getActiveSheet();


                // TITULO Encabezado
                $titulo = $curso->getDisciplina() . ' - ' . $curso->getDisciplina()->getDisciplinaGrupo();
                $encabezado = 'PREMATRICULAS ' . $curso->getDisciplina()->getDisciplinaGrupo() . ' - ' . $curso->getDisciplina();

                $sheet->setCellValue('A1', $encabezado);
                $sheet->mergeCells('A1:C1');
                $sheet->setTitle(substr($titulo, 0, 30));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'DDDDDD')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:C1")->applyFromArray($style);

                // ENCABEZADO tabla
                $sheet->setCellValue('A2', 'Identificador');
                $sheet->setCellValue('B2', 'Expediente');
                $sheet->setCellValue('C2', 'Nombre del solicitante');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B2B2B2')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A2:C2")->applyFromArray($style);

                // CONTENIDO DE LA TABLA
                $index2 = 3;
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'size' => 10,
                        'bold' => false
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFFF')
                    )
                );

                foreach ($prematriculas as $pre) {
                    $sheet->setCellValue('A' . $index2, $pre->getPrematricula()->getIdentificador());
                    $sheet->setCellValue('B' . $index2, $pre->getPrematricula()->getAlumno()->getExpediente());
                    $sheet->setCellValue('C' . $index2, $pre->getPrematricula()->getAlumno());
                    $sheet->getStyle("A" . $index2 . ":C" . $index2)->applyFromArray($style);
                    $index2++;
                }

                // Ancho de la columas automatico
                foreach (range('A', 'C') as $columnID)
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);

                $index++;
            }

        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $filename = "listado_prematriculas_".$this->slugify($cursoAcademico->getNombre()).'.xls';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * Genera el listado resultado del sorteo para pre-matrículas
     */
    public function generarListadoResultadoPrematriculas (CursoAcademico $cursoAcademico, $tipo = null) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator(self::creador)
            ->setLastModifiedBy(self::creador)
            ->setTitle(sprintf("Listado RESULTADO del SORTEO de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado RESULTADO del SORTEO de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado RESULTADO del SORTEO de pre-matriculados %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna resultado sorteo prematriculados curso%s", $this->slugify($cursoAcademico->getNombre())))
            ->setCategory("");

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $index = 0;
        foreach($cursos as $curso) {
            $prematriculas = $this->em->getRepository('AppBundle:PrematriculaEnCurso')->getPrematriculasOrdenLista($curso);
            if (count($prematriculas) > 0) {
                $phpExcelObject->createSheet();
                $phpExcelObject->setActiveSheetIndex($index);
                $sheet = $phpExcelObject->getActiveSheet();


                // TITULO Encabezado
                $titulo = $curso->getDisciplina() . ' - ' . $curso->getDisciplina()->getDisciplinaGrupo();
                $encabezado = 'PREMATRICULAS ' . $curso->getDisciplina()->getDisciplinaGrupo() . ' - ' . $curso->getDisciplina();

                $sheet->setCellValue('A1', $encabezado);
                $sheet->mergeCells('A1:E1');
                $sheet->setTitle(substr($titulo, 0, 30));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'DDDDDD')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:E1")->applyFromArray($style);

                // ENCABEZADO tabla
                $sheet->setCellValue('A2', 'Posición');
                $sheet->setCellValue('B2', 'Estado');
                $sheet->setCellValue('C2', 'Identificador');
                $sheet->setCellValue('D2', 'Expediente');
                $sheet->setCellValue('E2', 'Nombre del solicitante');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B2B2B2')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A2:E2")->applyFromArray($style);

                // CONTENIDO DE LA TABLA
                $index2 = 3;
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'size' => 10,
                        'bold' => false
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFFF')
                    )
                );

                foreach ($prematriculas as $pre) {
                    $sheet->setCellValue('A' . $index2, $pre->getNumeroLista());
                    $sheet->setCellValue('B' . $index2, $pre->getEstado());
                    $sheet->setCellValue('C' . $index2, $pre->getPrematricula()->getIdentificador());
                    $sheet->setCellValue('D' . $index2, $pre->getPrematricula()->getAlumno()->getExpediente());
                    $sheet->setCellValue('E' . $index2, $pre->getPrematricula()->getAlumno());
                    $sheet->getStyle("A" . $index2 . ":E" . $index2)->applyFromArray($style);
                    $index2++;
                }

                // Ancho de la columas automatico
                foreach (range('A', 'E') as $columnID)
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);

                $index++;
            }

        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $filename = "listado_resultado_sorteo_prematriculas_".$this->slugify($cursoAcademico->getNombre()).'.xls';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * Genera el listado de matriculas por cursos
     */
    public function generarListadoMatriculas(CursoAcademico $cursoAcademico) {
        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator(self::creador)
            ->setLastModifiedBy(self::creador)
            ->setTitle(sprintf("Listado matrículas del %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado matrículas del %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado matrículas del %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna listado matriculas curso%s", $this->slugify($cursoAcademico->getNombre())))
            ->setCategory("");

        $cursos = $this->em->getRepository('AppBundle:Curso')->findBy(array('cursoAcademico' => $cursoAcademico));

        $index = 0;
        foreach($cursos as $curso) {
            $matriculas = $curso->getMatriculas();
            if (count($matriculas) > 0) {
                $phpExcelObject->createSheet();
                $phpExcelObject->setActiveSheetIndex($index);
                $sheet = $phpExcelObject->getActiveSheet();

                // TITULO Encabezado
                $titulo = $curso->getDisciplina() . ' - ' . $curso->getDisciplina()->getDisciplinaGrupo();
                $encabezado = 'MATRICULAS ' . $curso->getDisciplina()->getDisciplinaGrupo() . ' - ' . $curso->getDisciplina();

                $sheet->setCellValue('A1', $encabezado);
                $sheet->mergeCells('A1:E1');
                $sheet->setTitle(substr($titulo, 0, 30));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'DDDDDD')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:E1")->applyFromArray($style);

                // ENCABEZADO tabla
                $sheet->setCellValue('A2', 'Expediente');
                $sheet->setCellValue('B2', 'Nombre');
                $sheet->setCellValue('C2', 'Fecha Nacimiento');
                $sheet->setCellValue('D2', 'Teléfono');
                $sheet->setCellValue('E2', 'Correo electrónico');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B2B2B2')
                    ),
                    'font' => array(
                        'size' => 12,
                        'bold' => true
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A2:E2")->applyFromArray($style);

                // CONTENIDO DE LA TABLA
                $index2 = 3;
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'size' => 10,
                        'bold' => false
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFFF')
                    )
                );

                foreach ($matriculas as $m) {
                    $sheet->setCellValue('A' . $index2, $m->getAlumno()->getExpediente());
                    $sheet->setCellValue('B' . $index2, $m->getAlumno());
                    $sheet->setCellValue('C' . $index2, $m->getAlumno()->getFechaNacimiento());
                    $sheet->setCellValue('D' . $index2, $m->getAlumno()->getTelefonos());
                    $sheet->setCellValue('E' . $index2, $m->getAlumno()->getEmail());
                    $sheet->getStyle("A" . $index2 . ":E" . $index2)->applyFromArray($style);
                    $index2++;
                }

                // Ancho de la columas automatico
                foreach (range('A', 'E') as $columnID)
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);

                $index++;
            }

        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $filename = "listado_resultado_sorteo_prematriculas_".$this->slugify($cursoAcademico->getNombre()).'.xls';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }

}