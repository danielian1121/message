<?php
require_once("myPDO.php");

if (isset($_GET['submit'])) {
}
$pdo = new myPDO;
$query = $pdo->bindQuery_noarray("SELECT * FROM article");
$json = json_encode($query);
echo $json;
