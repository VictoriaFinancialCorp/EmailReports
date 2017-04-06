<html>
<body>
  <h1>Reports</h1>
  <ul>
  <?php

    $dir = 'reports';
    $files = scandir($dir);

    foreach ($files as $file) {
      if(preg_match('/.php/',$file)){
        print "<li><a href='reports/$file'>$file</a></li>";
      }

    }
  ?>
  </ul>
</body>
</html>
