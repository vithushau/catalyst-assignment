<?php

namespace Commands;

use Exception;
use mysqli;

class UserCommand
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function execute(array $cli_options): void
    {
        if (isset($cli_options['create_table'])) {
            $this->createTable();
            return;
        }
        if (isset($cli_options['file'])) {
            $csvFile = $cli_options['file'];
            $dryRun = isset($cli_options['dry_run']);
            $this->processCSV($csvFile, $dryRun);
            return;
        }
        echo "Invalid command. Use --help for usage information.\n";
    }


    private function createTable(): void
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            surname VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL
            )";

            $stmt = $this->mysqli->prepare($sql);
            $stmt->execute();
            $stmt->close();
            echo "Table created successfully.\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }


    private function processCSV($csvFile, $dryRun): void
    {
        try {
            $handle = fopen($csvFile, "r");
            if ($handle !== FALSE) {
                $headerSkipped = false;

                if ($dryRun) {
                    echo "Dry Run Mode - Records Summary:\n";
                }

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if (!$headerSkipped) {
                        $headerSkipped = true;
                        continue;
                    }

                    $name = ucfirst($data[0]);
                    $surname = ucfirst($data[1]);
                    $email = strtolower($data[2]);

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo "Invalid email: $email\n";
                        continue;
                    }

                    $checkQuery = "SELECT COUNT(*) as count FROM users WHERE email = ?";
                    $check_stmt = $this->mysqli->prepare($checkQuery);

                    if (!$check_stmt) {
                        die("Error in SQL query: " . $this->mysqli->error);
                    }
                    $check_stmt->bind_param("s", $email);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    $row = $result->fetch_assoc();

                    if ($row['count'] != 0) {
                        echo "Duplicate email found in the database: $email\n";
                        continue;
                    }

                    $check_stmt->close();

                    $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?)";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("sss", $name, $surname, $email);

                    if (!$dryRun) {
                        if ($stmt->execute()) {
                            echo "Record inserted successfully\n";
                        } else {
                            echo "Error: " . $stmt->error . "\n";
                        }
                    } else {
                        echo "Name: $name, Surname: $surname, Email: $email\n";
                    }
                    $stmt->close();
                }
                fclose($handle);
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }


    }
}
