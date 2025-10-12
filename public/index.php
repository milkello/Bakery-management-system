<?php
require __DIR__ . '/../config/config.php';
// simple router
$page = $_GET['page'] ?? 'home';
$allowed = ['home','login','logout','dashboard','employees','employee_create','raw_materials','products',
            'recipes','production','sales','notifications','profile','change_password'];
if (!in_array($page,$allowed)) $page = 'home';
include __DIR__ . '/../app/views/header.php';
include __DIR__ . '/../app/controllers/' . $page . '.php';
include __DIR__ . '/../app/views/footer.php';
