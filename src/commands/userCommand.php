<?php

namespace Commands;

use mysqli;

class UserCommand
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function execute(array $cl_options): void
    {
        if (isset($cl_options['create_table'])) {
            $this->createTable();
        } elseif (isset($cl_options['file'])) {
            $csvFile = $cl_options['file'];
            $dryRun = isset($cl_options['dry_run']);
            $this->processCSV($csvFile, $dryRun);
        } else {
            echo "Invalid command. Use --help for usage information.\n";
        }
    }

    private function createTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            surname VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL
        )";

        if ($this->mysqli->query($sql) === TRUE) {
            echo "Table created successfully.\n";
        } else {
            echo "Error creating table: " . $this->mysqli->error . "\n";
        }
    }


    private function processCSV($csvFile, $dryRun): void
    {
        $handle = fopen($csvFile, "r");
        if ($handle !== FALSE) {
            $headerSkipped = false;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if (!$headerSkipped) {
                    $headerSkipped = true;
                    continue;
                }

                $name = strtolower($data[0]);
                $surname = strtolower($data[1]);
                $email = strtolower($data[2]);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "Invalid email: $email\n";
                    continue;
                }

                if (!$dryRun) {
                    // Insert data into database using prepared statement
                    $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?)";

                    // Prepare the statement
                    $stmt = $this->mysqli->prepare($sql);

                    // Bind parameters
                    $stmt->bind_param("sss", $name, $surname, $email);

                    // Execute the statement
                    if ($stmt->execute()) echo "Record inserted successfully"; else echo "Error: " . $stmt->error;

                    // Close the statement
                    $stmt->close();
                }

            }
            fclose($handle);
        }
    }
}
