<?php
$file = $_FILES['file'];
$newChunk = fopen($file['tmp_name'], 'rb');

$target = './uploads/' . $file['name'];

$writable = fopen($target, 'a+b');

$size = filesize($file['tmp_name']);

$data = fread($newChunk, $size);

fwrite($writable, $data);

fclose($writable);
fclose($newChunk);

?>