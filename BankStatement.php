<?php

abstract class BankStatement {
    protected $filePath;
    protected $headers = [];
    protected $rows = [];
    protected $encoding;
    protected $delimiter;

    public function __construct($filePath, $encoding = 'UTF-8', $delimiter = ';') {
        $this->filePath = $filePath;
        $this->encoding = $encoding;
        $this->delimiter = $delimiter;
        $this->readFile();
    }

    abstract public static function isMatchingFormat($fileName);

    abstract public function analyzeData();

    abstract public static function getBankName();

    private function readFile() {
        $content = file_get_contents($this->filePath);
        if ($this->encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $this->encoding);
        }
        $lines = explode("\n", $content);
        $this->headers = str_getcsv(array_shift($lines), $this->delimiter);
        foreach ($lines as $line) {
            if (trim($line)) {
                $this->rows[] = str_getcsv($line, $this->delimiter);
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
