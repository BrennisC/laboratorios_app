<?php
$to = $_GET['to'] ?? '/index.php';
// VULN: A05/A06 - Open redirect to arbitrary destination.
header('Location: ' . $to);
exit;
