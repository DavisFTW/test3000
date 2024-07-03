<?php
require 'vendor/autoload.php';

use App\Database\Database;

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Database connection successful.";
} else {
    echo "Database connection failed.";
}
