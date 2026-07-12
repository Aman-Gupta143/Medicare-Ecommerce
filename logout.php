<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

unset($_SESSION['user']);
setFlash('success', 'Logged out successfully.');
redirect('auth.php');