<?php
require_once __DIR__ . '/BankStatement.php';

class MonobankStatement extends BankStatement {
    public static function isMatchingFormat($fileName) {
        return strpos($fileName, 'report_') === 0;
    }

    public static function getBankName() { 
        return 'Монобанк';
    }

    public function __construct($filePath, $encoding = 'UTF-8') {
        parent::__construct($filePath, $encoding, ','); // Разделитель для Монобанка — запятая
    }

    public function processTransactions() {
        return null;
    }
    public function analyzeData(){
        return null;
    }
}
