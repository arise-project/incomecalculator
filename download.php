<?php

require_once __DIR__ . '/autoload.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!file_exists(__DIR__ . '/mergedData.json')) {
    die('Нет данных для скачивания.');
}

$mergedData = json_decode(file_get_contents(__DIR__ . '/mergedData.json'), true);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Общий отчёт');

// Заголовки
$headers = ['Дата', 'Безналичные Z', 'Сумма банка', 'Наличные Z', 'Разница Банк - Z безнал', 'Корриктеровка'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Данные
$rowNumber = 2;
foreach ($mergedData as $row) {
    $sheet->setCellValue('A' . $rowNumber, $row['date']);
    $sheet->setCellValue('B' . $rowNumber, $row['zSumNonCash']);
    $sheet->setCellValue('C' . $rowNumber, $row['bankSum']);
    $sheet->setCellValue('D' . $rowNumber, $row['zSumCash']);
    $sheet->setCellValue('E' . $rowNumber, $row['difference']);
    $sheet->setCellValue('F' . $rowNumber, $row['zCashCorrection']);

    $rowNumber++;
}

$filePath = __DIR__ . '/report.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filePath);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');
readfile($filePath);
unlink($filePath);
exit;
