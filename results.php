<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="src/style.css">
  <title>Messages results</title>
</head>
<body>
<?php
      try {      
        $MyDB = new PDO("mysql:host=labo-ve.fr;dbname=laboadm_mgoudj","laboadm_mehd_goud","fKh^gzx[8]EP", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
      } catch (Exception $ExceptionRaised) {
        die($ExceptionRaised);
      }
      $MyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      ?>  
  <div id="resultcontainer">

    <?php
      $dataToDisplay = $MyDB->query("SELECT * FROM t_msg ORDER BY date_msg DESC")->fetchall();
      foreach ($dataToDisplay as $row) {
        print '<div class="borderResult"><p>Sujet: ' . $row['sujet'] . ' Date:' . $row['date_msg'] . '</p><p>Message: ' . $row['msg'] . '</p></div>';
    }
    ?>
  </div>
</body>
</html>