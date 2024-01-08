<?php

namespace Database;

use mysqli;

class Connection
{
    public static function make($host, $database, $username, $password)
    {
        $mysqli = new mysqli($host, $username, $password, $database);

        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        return $mysqli;
    }
}
