<?php

class BankStatementManager {
    private $bankClasses = [
        //MonobankStatement::class,
        PrivatbankStatement::class,
        RaiffeisenbankStatement::class,
    ];
    public function processFile($filePath, $fileName) {
        foreach ($this->bankClasses as $bankClass) {
            if ($bankClass::isMatchingFormat($fileName)) {
                echo "Опеределен банк: ".$bankClass::getBankName()."<br/>";
                // Определяем кодировку файла
                $content = file_get_contents($filePath);
                
                //$encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true) ?: 'UTF-8';
                $detectedEncoding = mb_detect_encoding($content, ['Windows-1251', 'UTF-8'], true);

                if ($detectedEncoding !== 'UTF-8') {
                    $content = mb_convert_encoding($content, 'UTF-8', $detectedEncoding);
                }

                echo "encoding ".$detectedEncoding."<br/>";

                // Создаём объект соответствующего класса
                return new $bankClass($filePath, $detectedEncoding);
            }
        }

        throw new Exception("Не удалось определить банк для файла: $fileName");
    }
/*
    public function processFile($filePath, $fileName) {
        foreach ($this->bankClasses as $bankClass) {
            if ($bankClass::isMatchingFormat($fileName)) {
                // Определяем кодировку
                $content = file_get_contents($filePath);
                $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true) ?: 'UTF-8';

                // Обрабатываем файл
                $statement = new $bankClass($filePath, $encoding);
                return [
                    'headers' => $statement->getHeaders(),
                    'rows' => $statement->getRows(),
                ];
            }
        }
        throw new Exception("Не удалось определить банк для файла: $fileName");
    }*/
}
