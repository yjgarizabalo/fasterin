<?php
    require_once(APPPATH . 'libraries/phpoffice/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $nombre_Archivo = $nombre_excel . '.xlsx';

    $spreadsheet = new Spreadsheet();

    reset($datos_excel);
    $primer_dato = key($datos_excel);
    $estilos_titulo = [ 'font' => [ 'bold' => true ] ];

    foreach ($datos_excel as $tabla => $datosTabla) {
        if ($tabla == $primer_dato) $sheet = $spreadsheet->getActiveSheet();
        else $sheet = $spreadsheet->createSheet();

        $sheetName = ucfirst($tabla);
        $sheet->setTitle($sheetName);

        $letra_celda_titulo = 'A';
        foreach ($datosTabla[0] as $titulo) {
            $sheet->setCellValue($letra_celda_titulo . '1', $titulo);
            ++$letra_celda_titulo;
        }

        for($col = 'A'; $col !== $letra_celda_titulo; $col++) {
            if ($tabla == $primer_dato) {
                $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            } else {
                $spreadsheet->getSheetByName($sheetName)->getColumnDimension($col)->setAutoSize(true);
            }
        }
        $sheet->getStyle('A1:' . --$letra_celda_titulo . '1')->applyFromArray($estilos_titulo);

        $numero_celda = '2';
        foreach ($datosTabla[1] as $datosRegistro) {
            $letra_celda = 'A';
            foreach ($datosRegistro as $value) {
                $sheet->setCellValue($letra_celda . $numero_celda, $value);
                ++$letra_celda;
            }
            ++$numero_celda;
        }
        $sheet->getStyle('A1:' . --$letra_celda . --$numero_celda)->getAlignment()->setWrapText(true);
        
    }

    $maxWidth = 100;
    foreach ($spreadsheet->getAllSheets() as $sheet) {
        $sheet->calculateColumnWidths();
        foreach ($sheet->getColumnDimensions() as $colDim) {
            if (!$colDim->getAutoSize()) {
                continue;
            }
            $colWidth = $colDim->getWidth();
            if ($colWidth > $maxWidth) {
                $colDim->setAutoSize(false);
                $colDim->setWidth($maxWidth);
            }
        }
    }

    $writer = new Xlsx($spreadsheet);

    try {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $nombre_Archivo);
        $writer->save("php://output");
    } catch (Exception $e) {
        $e->getMessage();
    }
