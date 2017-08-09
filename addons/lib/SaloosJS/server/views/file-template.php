<?php foreach($f in $files) ?>
  <a href="#file/<?php echo $f['id']; ?>" id="file<?php echo $f['id']; ?>"><?php echo $f['name']; ?></a>
<?php endforeach; ?>