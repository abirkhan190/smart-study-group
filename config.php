<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=smart_study;charset=utf8mb4", "root", "",
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function auth(){ if(empty($_SESSION['uid'])){ header("Location: index.php"); exit; } }
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
