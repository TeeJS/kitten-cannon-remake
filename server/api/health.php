<?php
// Liveness probe for the container healthcheck (no DB dependency).
header('Content-Type: text/plain');
echo 'ok';
?>
