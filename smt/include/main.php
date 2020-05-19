<?php
function pdo_connect_mysql() {
  $server = "localhost";
  $username = "sds";
  $password = "sds_pass";
  $database = "sds";

  try {
// ERROR Handling from the constructor is somehow not working
//    return new PDO("mysql:host=$server;dbname=$database", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $conn = new PDO("mysql:host=$server;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
}

// These two are the same
function readableTime($tick) {
  $day = floor($tick/(100*60*60*24));
  $hour = floor(($tick - $day * 8640000) / 360000);
  $minute = floor(($tick - ($day * 8640000 + $hour * 360000)) / 6000);
  $second = floor(($tick - ($day * 8640000 + $hour * 360000 + $minute * 6000)) / 100);
  return $day."d $hour:$minute:$second";
}
function secondsToTime($deciSeconds) {
    $seconds = round($deciSeconds / 100);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a'.'d '.'%h:%i:%s');
}

function template_header($title) {
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>SDS Monitoring Tool</title>
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="styles/style.css">

    <style>
    </style>

    <script src="scripts/jquery-3.4.1.min.js"></script>
    <script src="scripts/functions.js"></script>
  </head>

  <body>
    <header>
      <h1>SDS Monitoring Tool</h1>
      <nav>
        <ul>
          <li><a href="index.php">Zariadenia</a></li>
          <li><a href="customers.php">Zákazníci</a></li>
          <li><a href="tools.php">Nástroje</a></li>
          <li><a href="reports.php">Reporty</a></li>
          <li><a href="about.php">INFO</a></li>
        </ul>
      </nav>
    </header>
EOT;
}

function template_footer() {
echo <<<EOT
    <footer>
      <p>Simple SDS Monitoring Tool for Telekom DC</p>
    </footer>
  </body>
</html>
EOT;
}
?>
