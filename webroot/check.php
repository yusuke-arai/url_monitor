<?php
require_once(__DIR__ . '/../const.php');

exec('python ' . HTTP_CHECK_SCRIPT, $output);
header('Location: .');
