<?php foreach($f in $folders) ?>
  <a href="#folder/<?php echo $f['id']; ?>" id="folder<?php echo $f['id']; ?>"><?php echo $f['name']; ?></a>
<?php endforeach; ?>