<?php
require_once("myPDO.php");

if (isset($_GET['submit'])) {
}
$pdo = new myPDO;
$query = $pdo->bindQuery_noarray("SELECT * FROM article");

// 後面參數可解決中文亂碼
$json = json_encode($query, JSON_UNESCAPED_UNICODE);

echo $json;
