<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);


require_once('myPDO.php');

if (isset($_POST["submit"])) {
    $pdo = new myPDO;
    $post=[
        "title" => $_POST["title"],
        "content" => $_POST["content"],
        "username" => $_POST["username"],
        "time" => "CURRENT_TIME()"
    ];
    $pdo->insert("article", $post);
}

?>

<html>
  <head>
    <meta http-equiv="refresh" content="5;url=create.html" />
    <title>Countdown Sample</title>
  </head>
  <body>
    you will be redirected to example.com in <span id="seconds">5</span>.
    <script>
      var seconds = 5;
      setInterval(
        function(){
          document.getElementById('seconds').innerHTML = --seconds;
        }, 1000
      );
    </script>
  </body>
</html>

