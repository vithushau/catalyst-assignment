<?php
require_once 'vendor/autoload.php';

use Database\Connection;
use Commands\UserCommand;

echo "Script started...\n";
$cli_options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);


if (isset($cli_options['help'])) {
    echo "Usage: php user_upload.php --file [csv file name] --create_table --dry_run -u [MySQL username] -p [MySQL password] -h [MySQL host] --help\n";
    exit();
}

// Database connection details
$host = $cli_options['h'] ?? 'localhost';
$username = $cli_options['u'] ?? 'root';
$password = $cli_options['p'] ?? '';
$database = 'catalyst';

$mysqli = Connection::make($host, $database, $username, $password);

$userCommand = new UserCommand($mysqli);

$userCommand->execute($cli_options);

echo "Script Completed...";