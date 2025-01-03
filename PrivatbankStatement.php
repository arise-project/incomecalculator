<?php

require_once __DIR__ . '/BankStatement.php';

class PrivatbankStatement extends BankStatement {
    public static function isMatchingFormat($fileName) {
        return preg_match('/^\d{16}\.csv$/', $fileName);
    }

    public function processTransactions() {
        return null;
    }

    public static function getBankName() { 
        return 'Приват банк';
    }


    public function analyzeData() {
        $dateIndex = array_search('Дата операції', $this->headers);
        $sumIndex = array_search('Сума', $this->headers);
        $descriptionIndex = array_search('Призначення платежу', $this->headers);

        if ($dateIndex === false || $sumIndex === false || $descriptionIndex === false) {
            throw new Exception("Не удалось найти необходимые столбцы в заголовках.");
        }

        $result = [];

        foreach ($this->rows as $row) {
            $date = isset($row[$dateIndex]) ? $row[$dateIndex] : null;
            $sum = isset($row[$sumIndex]) ? floatval(str_replace(' ', '', $row[$sumIndex])) : 0.0;
            $description = isset($row[$descriptionIndex]) ? $row[$descriptionIndex] : '';

            // Пропускаем строки с отрицательными или нулевыми суммами
            if ($sum <= 0) {
                continue;
            }

            // Извлечение комиссии из текста
            preg_match('/Ком бан ([\d\.]+)грн/ui', $description, $matches);
            $commission = isset($matches[1]) ? floatval(str_replace(' ', '', $matches[1])) : 0.0;

            // Сохранение данных
            $dateKey = $date; // Дата без обработки, так как в ПриватБанке уже без времени
            if (!isset($result[$dateKey])) {
                $result[$dateKey] = [
                    'date' => $dateKey,
                    'sum' => 0,
                    'commission' => 0,
                ];
            }

            $result[$dateKey]['sum'] += $sum;
            $result[$dateKey]['commission'] += $commission;
        }

        // Формирование итоговой суммы по дате
        foreach ($result as $date => &$data) {
            $data['total_sum'] = $data['sum'] + $data['commission'];
            unset($data['sum'], $data['commission']); // Убираем ненужные промежуточные данные
        }

        $resultArray = array_values($result);

        usort($resultArray, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $resultArray; // Возвращаем как массив
    }

    public function __construct($filePath, $encoding = 'Windows-1251') {
        parent::__construct($filePath, $encoding, ';'); // Разделитель для ПриватБанка — точка с запятой
    }
}
