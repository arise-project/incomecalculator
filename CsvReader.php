<?php

class CsvReader {
    private $filePath;
    private $headers = [];
    private $rows = [];

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->readFile();
    }

    private function detectEncoding($content) {
        // Определяем кодировку содержимого файла
        return mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true) ?: 'UTF-8';
    }

    private function readFile() {
        // Считываем файл
        $content = file_get_contents($this->filePath);

        // Определяем кодировку
        $encoding = $this->detectEncoding($content);

        // Преобразуем содержимое в UTF-8
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // Разбиваем файл на строки
        $lines = explode("\n", $content);

        // Первая строка — заголовки
        $this->headers = str_getcsv(array_shift($lines), ';'); 

        // Оставшиеся строки — данные
        foreach ($lines as $line) {
            if (trim($line)) {
                $this->rows[] = str_getcsv($line, ';');
            }
        }
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getRows() {
        return $this->rows;
    }
}

// Пример использования
try {
    $csvReader = new CsvReader('example.csv');
    $headers = $csvReader->getHeaders();
    $rows = $csvReader->getRows();    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
