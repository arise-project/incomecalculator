<?php

require_once __DIR__ . '/BankStatement.php';

class RaiffeisenbankStatement extends BankStatement {
    public static function isMatchingFormat($fileName) {
        $baseName = basename($fileName);
        return stripos($baseName, 'export') === 0;
    }

    public function __construct($filePath, $encoding = 'UTF-8') {
        parent::__construct($filePath, $encoding, ';');
    }

    public static function getBankName() { 
        return 'Аваль банк';
    }

    public function analyzeData() {

        $dateIndex = array_search('Дата операції', $this->headers);
        $sumIndex = array_search('Кредит', $this->headers);
        $descriptionIndex = array_search('Призначення платежу', $this->headers);

        $result = [];

        foreach ($this->rows as $row) {
            
            $date = isset($row[$dateIndex]) ? explode(' ', $row[$dateIndex])[0] : null;
            $sum = isset($row[$sumIndex]) ? floatval($row[$sumIndex]) : 0.0;
            $description = isset($row[$descriptionIndex]) ? $row[$descriptionIndex] : '';
            
            // Ищем комиссию в тексте
            preg_match('/комісія ([\d\.]+)/ui', $description, $matches);
            $commission = isset($matches[1]) ? floatval($matches[1]) : 0.0;

            if ($sum > 0) {
                $result[$date]['sum'] = ($result[$date]['sum'] ?? 0) + $sum;
                $result[$date]['commission'] = ($result[$date]['commission'] ?? 0) + $commission;
            }
        }

        // Суммируем сумму и комиссию
        foreach ($result as $date => $data) {
            $result[$date] = [
                'date' => $date,
                'total_sum' => $data['sum'] + $data['commission']
            ];
        }

        return $result;
    }
    
    public function processTransactions() {
        return null;
    }
}
