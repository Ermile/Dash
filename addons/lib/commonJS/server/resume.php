<?php

$size = filesize('./uploads/' . $_POST['fileName']);

echo $size;

?>