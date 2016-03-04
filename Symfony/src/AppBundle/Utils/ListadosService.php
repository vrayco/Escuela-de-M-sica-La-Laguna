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
    private $em = null;
    private $phpExcel;

    public function __construct(EntityManager $em, PHPExeclFactory $phpExcel)
    {
        $this->em = $em;
        $this->phpExcel = $phpExcel;
    }

    public function generarListadoPreinscripciones (CursoAcademico $cursoAcademico) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Escula de Música La Laguna - App")
            ->setTitle(sprintf("Listado de pre-inscriptos %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado de pre-inscriptos %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado de pre-inscriptos %s", $cursoAcademico->getNombre()))
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
                $preinscripciones = $this->em->getRepository('AppBundle:PreinscripcionEnCurso')->getPreinscripcionesOrdenAlfabetico($curso);
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
                $stylePrioridad = array(
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
                        'color' => array('rgb' => 'EFEFEF')
                    )
                );
                foreach($preinscripciones as $pre) {
                    $sheet->setCellValue('A'.$index2, $pre->getPreinscripcion()->getIdentificador());
                    $sheet->setCellValue('B'.$index2, $pre->getPreinscripcion()->getApellidos().', '.$pre->getPreinscripcion()->getNombre());
                    $sheet->setCellValue('C'.$index2, $pre->getPreinscripcion()->getFechaNacimiento()->format('d/m/Y'));
                    $sheet->setCellValue('D'.$index2, $pre->getPreinscripcion()->getPrioridad() ? 'Si' : 'No');
                    $sheet->setCellValue('E'.$index2, $pre->getPreinscripcion()->getEmpadronado() ? 'Si' : 'No');

                    if($pre->getPreinscripcion()->getPrioridad())
                        $sheet->getStyle("A".$index2.":E".$index2)->applyFromArray($stylePrioridad);
                    else
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

    public function generarListadoCursos (CursoAcademico $cursoAcademico) {

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Escula de Música La Laguna - App")
            ->setTitle(sprintf("Listado de %s", $cursoAcademico->getNombre()))
            ->setSubject(sprintf("Listado de %s", $cursoAcademico->getNombre()))
            ->setDescription(sprintf("Listado de %s", $cursoAcademico->getNombre()))
            ->setKeywords(sprintf("escuela-musica-lalaguna listado curso%s", $this->slugify($cursoAcademico->getNombre())))
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
                $stylePrioridad = array(
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
                        'color' => array('rgb' => 'EFEFEF')
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

                    if($pre->getPreinscripcion()->getPrioridad())
                        $sheet->getStyle("A".$index2.":G".$index2)->applyFromArray($stylePrioridad);
                    else
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