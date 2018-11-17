<?php
/************************************************************************
 *
 *  Домашнее задание от 15.11.18
 *  Вывести таблицу с полями
 *  id, user_link, comment, category,total_spent, created_at,
 *  где номер категории заменить на название категории,
 *  updated_at привести к формату год-месяц-день
 *  и total_spent вывести со знаком валюты
 *
 *
 ************************************************************************/

$descParam = '-'; // начальное значение параметра типа сортировки для формирования ссылки
$arrow = '&uarr;'; // начальное значение типа стрелки

# Json со списом категорий
$categoryJson = '{"4":"Assets","7":"Christmas","2":"Clothes","3":"Easter","5":"Gameplay","8":"Halloween","6":"Release theme","1":"Scenery","10":"St. Patrick\'s","9":"St.Valentine","11":"Stylist"}';
$category = json_decode($categoryJson, true);// преобразование json строки в массив

$descStatus = false; // начальное значение для переменной хранящей состояние обратной сортировки для проверки условий
if ($_REQUEST['sort'][0] == '-') { // проверка параметра пришедшего из ссылки. если условие выполняется меняем значения по умолчанию
    $descStatus = true;
    $descParam = '';
    $arrow = '&darr;';
}

$fp = fopen('testJsonDataToSort.txt', 'r'); // закрепляет именованый ресурс, указанный в аргументе filename, за потоком.
$mytext = ''; // пустая переменная для записи данных из файла
while (!feof($fp)) { // проверка что указатель файла не достиг End Of File (EOF)
    $mytext .= fgets($fp); // функция берет чанк символо узаканной длинный из файла
}
fclose($fp); // закрывает поток

$arr = json_decode($mytext, true); // преобразование json строки в массив

foreach ($arr as $key => $row) {
    $arr[$key]['category'] = $category[$row['category']]; // замена номеров категорий на их имена
}

# сортировка массива на основе параметров запроса
switch (str_replace('-', '', $_REQUEST['sort'])) {
    case 'category':
        if ($descStatus) {
            function mnsort($a, $b)
            {
                return strnatcmp($b['category'], $a['category']); // применение человекоподобного алгоритма для сортировки строк
            }
        } else {
            function mnsort($a, $b)
            {
                return strnatcmp($a['category'], $b['category']);
            }
        }
        break;
    default:
        if ($descStatus) {
            function mnsort($a, $b)
            {
                $sortName = str_replace('-', '', $_REQUEST['sort']); // удаление лишнего символа для получения текущего поля сортировки
                return $b[$sortName] > $a[$sortName]; // сравнение числовых значений для сортировки
            }
        } else {
            function mnsort($a, $b)
            {
                return $a[$_REQUEST['sort']] > $b[$_REQUEST['sort']];
            }
        }
        break;
}

$sortOrder = 'arrow' . str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace('-', '', $_REQUEST['sort'])))); // формирование имени переменной хранящей текущую стрелку
$$sortOrder = $arrow; // присвоение текущей стрелки
usort($arr, 'mnsort'); // применение сортировки инициализированной в switch функции

include ('template.html'); // подключение представления