<?php

require_once __DIR__ . '/autoload.php';
/*
function mergeReportAndStatement($bankData, $zReportData) {
    $mergedData = [];

    // Приводим даты в массиве bankData к единому формату
    foreach ($bankData as &$entry) {
        $entry['date'] = date('d.m.Y', strtotime($entry['date']));
    }

    // Приводим ключи в zReportData к единому формату
    $formattedZReportData = [];
    foreach ($zReportData as $dateKey => $entry) {
        $formattedDate = date('d.m.Y', strtotime($dateKey));
        $formattedZReportData[$formattedDate] = $entry;
    }

    // Получаем уникальные даты из обоих массивов
    $dates = array_unique(array_merge(
        array_column($bankData, 'date'),
        array_keys($formattedZReportData)
    ));
    sort($dates);

    // Подготовка массива $shiftedNonCash
    $shiftedNonCash = [];
    $previousNonCash = 0;

    foreach ($formattedZReportData as $date => $entry) {
        $shiftedNonCash[$date] = $previousNonCash;
        $previousNonCash = $entry['nonCash'];
    }

    // Сопоставление данных по датам
    foreach ($dates as $date) {
        // Поиск записи банка
        $bankEntry = array_filter($bankData, fn($entry) => $entry['date'] === $date);
        $bankSum = $bankEntry ? current($bankEntry)['total_sum'] : null;

        // Поиск записи Z-отчета
        $zReportEntry = $formattedZReportData[$date] ?? null;
        $zSumCash = $zReportEntry['cash'] ?? null;
        $zSumNonCashShifted = $shiftedNonCash[$date] ?? null;

        // Добавляем строку только если хотя бы один источник содержит данные
        if ($bankSum !== null || $zSumCash !== null || $zSumNonCashShifted !== null) {
            $mergedData[] = [
                'date' => $date,
                'zSumNonCash' => $zSumNonCashShifted,
                'bankSum' => $bankSum,
                'zSumCash' => $zSumCash,
            ];
        }
    }

    return $mergedData;
}*/
/*
function mergeReportAndStatement($bankData, $zReportData) {
    $mergedData = [];

    // Приводим даты в массиве bankData к единому формату
    foreach ($bankData as &$entry) {
        $entry['date'] = date('d.m.Y', strtotime($entry['date']));
    }

    echo "Bank data<br/>";
    var_dump($bankData);

    // Приводим ключи и значения в zReportData к единому формату
    foreach ($zReportData as $dateKey => $entry) {
        $entry['date'] = date('d.m.Y', strtotime($dateKey));
        $formattedZReportData[] = $entry;
    }

    // Получаем уникальные даты из обоих массивов
    $dates = array_unique(array_merge(
        array_column($bankData, 'date'),
        array_column($formattedZReportData, 'date')
    ));
    sort($dates);

    // Подготовка массива $shiftedNonCash
    $shiftedNonCash = [];
    $previousNonCash = 0;

    foreach ($formattedZReportData as $entry) {
        $shiftedNonCash[$entry['date']] = $previousNonCash;
        $previousNonCash = $entry['nonCash'];
    }

    echo "formattedZReportData data<br/>";
    var_dump($formattedZReportData);

    // Сопоставление данных по всем датам
    foreach ($dates as $date) {
        // Поиск записи банка
        $bankEntry = array_filter($bankData, fn($entry) => $entry['date'] === $date);
        $bankSum = $bankEntry ? current($bankEntry)['total_sum'] : null;

        // Поиск записи Z-отчета
        $zReportEntry = array_filter($formattedZReportData, fn($entry) => $entry['date'] === $date);
        $zSumCash = $zReportEntry ? current($zReportEntry)['cash'] : null;
        $zSumNonCashShifted = $shiftedNonCash[$date] ?? null;

        // Добавляем данные
        $mergedData[] = [
            'date' => $date,
            'zSumNonCash' => $zSumNonCashShifted,
            'bankSum' => $bankSum,
            'zSumCash' => $zSumCash,
        ];
    }

    echo "merged data<br/>";
    var_dump($mergedData);

    return $mergedData;
}*/

function isAssociativeArray(array $array): bool {
    foreach (array_keys($array) as $key) {
        if (!is_int($key)) {
            return true;
        }
    }
    return false;
}

function mergeReportAndStatement($bankData, $zReportData) {
    $mergedData = [];

    // Приводим даты в массиве bankData к единому формату
    foreach ($bankData as $key => $entry) {
        $bankData[$key]['date'] = date('d.m.Y', strtotime($entry['date']));
    }

    if (!isAssociativeArray($bankData)) {
        // Преобразуем $bankData в ассоциативный массив
        $bankDataAssoc = [];
        foreach ($bankData as $entry) {
            $bankDataAssoc[$entry['date']] = $entry;
        }
        $bankData = $bankDataAssoc;
    }

    // Приводим ключи и значения в zReportData к единому формату
    $formattedZReportData = [];
    foreach ($zReportData as $dateKey => $entry) {
        $entry['date'] = date('d.m.Y', strtotime($dateKey));
        $formattedZReportData[$entry['date']] = $entry;
    }

    // Получаем уникальные даты из обоих массивов
    $dates = array_unique(array_merge(
        array_column($bankData, 'date'),
        array_keys($formattedZReportData)
    ));
    sort($dates);
    
    // Подготовка массива $shiftedNonCash
    $shiftedNonCash = [];
    $previousNonCash = 0;

    foreach ($dates as $date) {
        // Заполняем массив для сдвинутых nonCash
        if (isset($formattedZReportData[$date])) {
            $shiftedNonCash[$date] = $previousNonCash;
            $previousNonCash = $formattedZReportData[$date]['nonCash'];
        } else {
            $shiftedNonCash[$date] = $previousNonCash;
        }
    }

    // Сопоставление данных по всем датам
    foreach ($dates as $date) {


        $bankEntry = array_key_exists($date, $bankData) ? $bankData[$date] : null;
        $bankSum = $bankEntry['total_sum'] ?? null;

        // Найти запись Z-отчета
        $zReportEntry = $formattedZReportData[$date] ?? null;
        $zSumCash = $zReportEntry['cash'] ?? null;
        $zSumNonCashShifted = $shiftedNonCash[$date] ?? null;

        // Добавляем данные
        $mergedData[] = [
            'date' => $date,
            'zSumNonCash' => $zSumNonCashShifted,
            'bankSum' => $bankSum,
            'zSumCash' => $zSumCash,
        ];
    }

    return $mergedData;
}


$zReportData = [];
$bankData = [];
$mergedData = [];

$accumulatedData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $files = $_FILES['files'];

    $manager = new BankStatementManager();

    foreach ($files['tmp_name'] as $index => $tmpPath) {
        $originalName = $files['name'][$index];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (in_array($extension, ['xls', 'xlsx'])) {
            try {
                require_once __DIR__ . '/SimpleXLSX.php';
                $zReportReader = new ZReportReader($tmpPath);
                $zReportData = $zReportReader->getRows();

                foreach ($zReportData as $row) {
                    $date = $row['date'];
                    $cash = $row['cash'];
                    $nonCash = $row['nonCash'];

                    if (!isset($accumulatedData[$date])) {
                        $accumulatedData[$date] = [
                            'date' => $date,
                            'cash' => 0,
                            'nonCash' => 0,
                        ];
                    }

                    $accumulatedData[$date]['cash'] += $cash;
                    $accumulatedData[$date]['nonCash'] += $nonCash;
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Ошибка обработки файла $originalName: " . $e->getMessage() . "</p>";
            }
        }

        
        if (in_array($extension, ['csv'])) {
            try {

                $statement = $manager->processFile($tmpPath, $originalName);
                $analyzedData = $statement->analyzeData();
                if (!empty($analyzedData)) {
                    $bankData = array_merge($bankData, $analyzedData);
                }                  

            } catch (Exception $e) {
                echo "<p style='color: red;'>Ошибка обработки файла $originalName: " . $e->getMessage() . "</p>";
            }
        }
    }
    


    if (!empty($accumulatedData) && !empty($bankData)) {
        
        $mergedData = mergeReportAndStatement($bankData, $accumulatedData);
        
    } else {
        echo "<p style='color: red;'>Ошибка: недостаточно данных для объединения.</p>";
    }


    //var_dump($bankData);

    ?>

    <h1 style="text-align: center;">Банковская выписка</h1>
    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bankData as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['date']) ?></td>
                    <td><?= htmlspecialchars(number_format($entry['total_sum'], 2, '.', ' ')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Сгруппированные данные банковской выписки</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Дата</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Группируем данные по датам
            $groupedBankData = [];
            foreach ($bankData as $entry) {
                $date = $entry['date'];
                $totalSum = $entry['total_sum'];

                if (!isset($groupedBankData[$date])) {
                    $groupedBankData[$date] = 0;
                }
                $groupedBankData[$date] += $totalSum;
            }

            // Вывод сгруппированных данных
            foreach ($groupedBankData as $date => $totalSum): ?>
                <tr>
                    <td><?= htmlspecialchars($date) ?></td>
                    <td><?= htmlspecialchars(number_format($totalSum, 2)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php

    foreach ($mergedData as &$entry) {

        $bankValue = isset($entry['bankSum']) ? $entry['bankSum'] : 0;
        $znoCashValue = isset($entry['zSumNonCash']) ? $entry['zSumNonCash'] : 0;

        // Добавляем столбец разницы
        $entry['difference'] = round($bankValue - $znoCashValue, 2);

        if ($entry['difference'] > 0){
            $entry['zCashCorrection'] = $entry['zSumCash'] - $entry['difference'];
        } else if ($entry['difference'] < 0){ 
            $entry['zCashCorrection'] = $entry['zSumCash'] + $entry['difference'];
        } else {
            $entry['zCashCorrection'] = $entry['zSumCash'];    
        }    
    }

    file_put_contents(__DIR__ . '/mergedData.json', json_encode($mergedData));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Данные</title>
</head>
<body>
    <h1>Объединённые данные</h1>
    <?php if (!empty($mergedData)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Безналичные Z</th>
                    <th>Сумма банка</th>
                    <th>Наличные Z</th>
                    <th>Разница Банк - Z</th>
                    <th>Z - нал коррекция</th>
                </tr>
            </thead>
            <tbody>            
                <?php foreach ($mergedData as $row): ?>                    
                    <tr style="<?= $row['difference'] != 0 ? 'color: red;' : '' ?>">
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['zSumNonCash']) ?></td>
                        <td><?= htmlspecialchars($row['bankSum']) ?></td>
                        <td><?= htmlspecialchars($row['zSumCash']) ?></td>
                        <td><?= htmlspecialchars($row['difference']) ?></td>
                        <td><?= htmlspecialchars($row['zCashCorrection']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <form action="download.php" method="post">
            <button type="submit">Скачать файл</button>
        </form>
    <?php else: ?>
        <p>Нет данных для отображения.</p>
    <?php endif; ?>
</body>
</html>
