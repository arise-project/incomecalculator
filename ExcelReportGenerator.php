<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Behat\Transliterator\Transliterator;

class ExcelReportGenerator {
    private $spreadsheet;

    public function __construct() {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);        
    }

    private function transliterate($string) {
        // Используем библиотеку Behat Transliterator для преобразования
        //return Transliterator::transliterate($string);
        //$string = Transliterator::transliterate($string);
        return mb_convert_encoding($string, 'UTF-8', 'auto');
    }

    public function addDataToSingleSheet($sheetTitle, $dataSets) {
        
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($this->transliterate($sheetTitle)); // Транслитерируем заголовок листа

        $rowNumber = 1;

        foreach ($dataSets as $title => $data) {
            // Транслитерация заголовков
            $sheet->setCellValue("A$rowNumber", $this->transliterate($title));
            $sheet->mergeCells("A$rowNumber:E$rowNumber");
            $sheet->getStyle("A$rowNumber")->getFont()->setBold(true);
            $rowNumber++;

            if (empty($data)) {
                $sheet->setCellValue("A$rowNumber", $this->transliterate("Нет данных"));
                $rowNumber += 2;
                continue;
            }

            // Заголовки таблицы
            $headers = array_keys(current($data));
            $col = 'A';

            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}{$rowNumber}", $this->transliterate($header));
                $col++;
            }

            $rowNumber++;

            // Данные таблицы
            foreach ($data as $row) {
                $col = 'A';
                foreach ($row as $value) {
                    $sheet->setCellValue("{$col}{$rowNumber}", $this->transliterate($value));
                    $col++;
                }
                $rowNumber++;
            }

            $rowNumber += 2; // Пропускаем две строки между таблицами
        }
    }

    public function saveAndDownload($filePath) {
        try {
            $writer = new Xls($this->spreadsheet);
            $writer->save($filePath);
            
            // Отправляем файл пользователю
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . basename($filePath) . '"');
            header('Cache-Control: max-age=0');
            readfile($filePath);

            // Удаляем временный файл
            unlink($filePath);
        } catch (Exception $e) {
            die("Ошибка при сохранении файла: " . $e->getMessage());
        }
    }
}