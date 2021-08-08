<?php
 //реализация не оптимизирована по времени
 //учитывая пропорции изображения в данной задаче
 //20000/20000 input против 200/100 output
 //лучше воспользоваться стандартными пакетами, предоставляемыми самитм языком
 //изобретение велосипеда может не только ударить по производительнотсти
 //но и заставит всерьез задуматься о безопасности
 //вводимые форматы изображений необходимо фильтравать, как и размеры
 //нельзя исключать того сценария, что злоумышленник может воспользоваться
 //пэйлоадом-полиглотом и обойти рукописные фильтры, установленные для форматов изображений
//кроме того, нельзя исключать возможности стеганографии и преобразование форматов уже на сервере
//так что для решения задачи преобразования изображения стоит предпочесть оптимизированные
//и безопасные скрипты, поставляемые в соответствующих модулях для языка


// пути файлов
$path = 'i/';
$tmp_path = 'tmp/';
// Допустимые типы
$types = array('image/gif', 'image/png', 'image/jpeg');
// ьаксимальный размер 
$size = 2000000;
 
// запрос
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
 // проверка тип файла
 if (!in_array($_FILES['picture']['type'], $types))
 die('<p>Запрещённый тип файла. <a href="?">Попробовать другой файл?</a></p>');
 
 // проверка размера файла
 if ($_FILES['picture']['size'] > $size)
 die('<p>Слишком большой размер файла. <a href="?">Попробовать другой файл?</a></p>');
 
 // функция изменения размера
 function resize($file, $type = 1,  $quality = null)
 {
 global $tmp_path;
 
 // граница ширины
 $max_thumb_size = 200;
 $max_size = 100;
 
 // качество
 if ($quality == null)
 $quality = 75;
 
 // исходное изображение
 if ($file['type'] == 'image/jpeg')
 $source = imagecreatefromjpeg($file['tmp_name']);
 elseif ($file['type'] == 'image/png')
 $source = imagecreatefrompng($file['tmp_name']);
 elseif ($file['type'] == 'image/gif')
 $source = imagecreatefromgif($file['tmp_name']);
 else
 return false;
 $src = $source;
 
 // пределяем ширину и высоту изображения
 $w_src = imagesx($src); 
 $h_src = imagesy($src);
 
 // устанавливаем ограничение по ширине.
 if ($type == 1)
 $w = $max_thumb_size;
 elseif ($type == 2)
 $w = $max_size;
 
 // ширина больше
 if ($w_src > $w)
 {
 // пропорции
 $ratio = $w_src/$w;
 $w_dest = round($w_src/$ratio);
 $h_dest = round($h_src/$ratio);
 
 // пустая картинка
 $dest = imagecreatetruecolor($w_dest, $h_dest);
 
 //копия изображения с изменением параметров
 imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);
 
 // вывод и уборка мусора
 imagejpeg($dest, $tmp_path . $file['name'], $quality);
 imagedestroy($dest);
 imagedestroy($src);
 
 return $file['name'];
 }
 else
 {
 imagejpeg($src, $tmp_path . $file['name'], $quality);
 imagedestroy($src);
 
 return $file['name'];
 }
 }
 
 $name = resize($_FILES['picture'], $_POST['file_type'], $_POST['file_rotate']);
 
 // загружаем
 if (!@copy($tmp_path . $name, $path . $name))
 echo '<p>что-то не так</p>';
 else
 echo '<p>Загрузка прошла удачно <a href="' . $path . $_FILES['picture']['name'] . '">Посмотреть</a>.</p>';
 
 //ликвидируем временный файл
 unlink($tmp_path . $name);
}
 
?>