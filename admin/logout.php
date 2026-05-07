<?php
require __DIR__ . '/config.php';
admin_logout();
header('Location: ' . admin_base_url() . '/admin/');
exit;
