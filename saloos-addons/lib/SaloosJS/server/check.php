<?php

$file = $_FILES['file'];

$read_from = './uploads/' . $file['name'];

$readable = fopen($read_from, 'r');
$size = filesize($read_from);

$range = explode('-', $_POST['range']);
$offset = (int)($range[0]);
$len = (int)($range[1]) - (int)($range[0]);

fseek($readable, $offset);
$firstBytes = fread($readable, $len);

$chunk = fopen($file['tmp_name'], 'r');

$chunkBytes = fread($chunk, filesize($file['tmp_name']));

$compare = strcmp($chunkBytes, $firstBytes);

if($compare == 0) {
  http_response_code(200);
} else {
  http_response_code(400);
}

?>
