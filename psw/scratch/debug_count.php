<?php
require_once 'php/config.php';
$db = getDB();
foreach($db->query('SELECT id, name, description FROM products WHERE id IN (11,12,14)')->fetchAll() as $p) {
    echo $p['id'] . ': ' . $p['name'] . ' -> ' . $p['description'] . "\n";
}



