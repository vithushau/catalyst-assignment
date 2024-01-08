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


if (isset($cli_options['u']) && isset($cli_options['p']) && isset($cli_options['h'])) {

    // Database connection details
    $host = $cli_options['h'];
    $username = $cli_options['u'];
    $password = $cli_options['p'];
    $database = 'catalys'; // add your database name
    $mysqli = Connection::make($host, $database, $username, $password);

    $userCommand = new UserCommand($mysqli);

    $userCommand->execute($cli_options);
} else {
    echo "Error: MySQL username (-u), password (-p), and host (-h) are required when using --file or --create_table.\n";
}

echo "Script Completed...";