<?php
require_once __DIR__ . '/lib/security.php';
log_security_event('logout', 'User logged out');
$_SESSION = [];
session_destroy();
redirect('/index.php');
