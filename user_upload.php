<?php
require_once 'vendor/autoload.php';

use Database\Connection;
use Commands\UserCommand;

echo "Script started...\n";
$cl_options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);


if (isset($cl_options['help'])) {
    echo "Usage: php user_upload.php --file [csv file name] --create_table --dry_run -u [MySQL username] -p [MySQL password] -h [MySQL host] --help\n";
    exit();
}

// Database connection details
$host = $cl_options['h'] ?? 'localhost';
$username = $cl_options['u'] ?? 'root';
$password = $cl_options['p'] ?? '';
$database = 'catalyst';

// Create MySQLi connection
$mysqli = Connection::make($host, $database, $username, $password);

// Create an instance of UserCommand
$userCommand = new UserCommand($mysqli);

// Execute the command
$userCommand->execute($cl_options);

echo "Script Completed...";