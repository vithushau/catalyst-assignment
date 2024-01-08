<?php

namespace Commands;

use Exception;
use mysqli;

// Define the maximum length for fgetcsv
define('MAX_LINE_LENGTH', 1000);

class UserCommand
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function execute(array $options): void
    {
        if (isset($options['create_table'])) {
            $this->createTable();
            return;
        }
        if (isset($options['file'])) {
            $csvFile = $options['file'];
            $dryRun = isset($options['dry_run']);
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
            if ($handle === FALSE) {
                echo "The file does not exist or could not be opened.";
            } else {
                $headerSkipped = FALSE;
                $fileEmails = [];

                if ($dryRun) {
                    echo "Dry Run Mode - Records Summary:\n";
                }

                while (($data = fgetcsv($handle, MAX_LINE_LENGTH, ",")) !== FALSE) {

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
                    $checkStmt = $this->mysqli->prepare($checkQuery);
                    if (!$checkStmt) {
                        die("Error in SQL query: " . $this->mysqli->error);
                    }
                    $checkStmt->bind_param("s", $email);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $row = $result->fetch_assoc();
                    if ($row['count'] != 0) {
                        echo "Duplicate email found in the database: $email\n";
                        continue;
                    }
                    $checkStmt->close();

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
                        // Check if the email is duplicated in the file
                        if (in_array($email, $fileEmails)) {
                            echo "Duplicate email found in the file: $email\n";
                            continue;
                        }
                        $fileEmails[] = $email;
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
