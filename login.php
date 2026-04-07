<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$target = 'index.php' . ($query !== '' ? ('?' . $query) : '');
header('Location: ' . $target);
exit;
?>