<?php
//подключение классов
require_once 'MyLogger.php';
require_once 'PDOAdapter.php';

//подключение к БД создание объекта ч/з конструктор
$connect=new PDOAdapter ('mysql:host=localhost;dbname=test','root','test','err.txt');

echo '
<!DOCTYPE HTML>
<html>
 <head>
  <meta charset="utf-8">
  <title>Результат выполнения заданий</title>
 </head>
 <body>
';

//Определить максимальный возраст
$query1 = $connect->execute('selectOne','SELECT Max(age) as AGE FROM person');
//print_r($query);
echo '<b>Максимальный возраст группы: </b>'.$query1->AGE.' лет</br></br>';

//Найти любую персону, у которой mother_id не задан и возраст меньше максимального
$query2 = $connect->execute('selectOne','SELECT * FROM `person` WHERE mother_id is NULL and age<(SELECT max(age) from person) ORDER BY RAND() limit 1');
echo '<b>Случайная персона, у которой mother_id не задан и возраст меньше максимального: </b>'.$query2->lastname.' '.$query2->firstname.' '.$query2->age.' лет</br></br>';

//изменить у найденной персоны возраст на максимальный
$array_update = [
    "select_person" => $query2->id,
    "max_age" => $query1->AGE,
];
$connect->execute('execute','UPDATE person SET age=:max_age WHERE id=:select_person',$array_update);

/*
/ Заголовок "Список персон максимального возраста (здесь значение п.1)"
/ Таблица, содержащая колонки: фамилия, имя, возраст.
/ В строках таблицы - персоны, упорядоченные по возрастанию фамилии и имени.
*/
$query3 = $connect->execute('selectAll','SELECT * FROM person WHERE age=(SELECT MAX(age) from person) order by lastname,firstname');

echo'
<table border="1" width="30%">
   <caption>'."<b>Список персон максимального возраста: ".$query1->AGE.'</b></caption>
   <tr>
    <th>Фамилия</th>
    <th>Имя</th>
    <th>Возраст</th>
   </tr>';
   foreach ($query3 as $data_mass){
   echo '<tr align="center">
   <td>'.$data_mass->lastname.'</td>'.
   '<td>'.$data_mass->firstname.'</td>'.
   '<td>'.$data_mass->age.'</td>
   </tr>';
   }
  echo '
  </table>
';
echo '
</body>
</html>';
?>