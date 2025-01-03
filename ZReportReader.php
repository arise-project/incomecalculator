<?php

require_once __DIR__ . '/SimpleXLSX.php';

class ZReportReader {
    private $filePath;
    private $rows = [];

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->readFile();
    }

    private function readFile() {
        if ($xlsx = SimpleXLSX::parse($this->filePath)) {
            $data = $xlsx->rows();

            // Извлекаем индексы нужных столбцов
            $headers = array_shift($data);
            $dateIndex = array_search('OrderDateTime', $headers);
            $cashIndex = array_search('RlzSumCash', $headers);
            $nonCashIndex = array_search('RlzSumNonCash', $headers);

            /*
            print_r($headers);
            print_r($dateIndex);
            print_r($cashIndex);
            print_r($nonCashIndex);
            */

            if ($dateIndex === false || $cashIndex === false || $nonCashIndex === false) {
                throw new Exception("Не удалось найти необходимые столбцы.");
            }

            foreach ($data as $row) {
                //$date = $row[$dateIndex] ?? null;
                $date = isset($row[$dateIndex]) ? explode(' ', $row[$dateIndex])[0] : null;
                $cash = isset($row[$cashIndex]) ? floatval(str_replace(' ', '', $row[$cashIndex])) : 0.0;
                $nonCash = isset($row[$nonCashIndex]) ? floatval(str_replace(' ', '', $row[$nonCashIndex])) : 0.0;

                if ($date) {
                    $this->rows[] = [
                        'date' => $date,
                        'cash' => $cash,
                        'nonCash' => $nonCash,
                    ];
                }
            }

            // Сортировка по дате
            usort($this->rows, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
        } else {
            throw new Exception(SimpleXLSX::parseError());
        }
    }

    public function getRows() {
        return $this->rows;
    }
}

