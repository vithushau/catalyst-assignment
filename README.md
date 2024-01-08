## User Upload PHP Script
This PHP script is designed to process a CSV file containing user data and insert it into a MySQL database.

## Prerequisites

- PHP version: 8.1.x.
- MySQL database server: Version 5.7 or higher (MariaDB 10.x is also supported).
- Operating System: Ubuntu 22.04.
- Git for version control

## Installation

Clone the repository to a directory.

```
git clone [repository_url]
cd [repository_directory]
```

## Usage

Replace the database name.

The script can be executed from the command line with the following directives:

```
php user_upload.php --file users.csv --dry_run -u root -h localhost -p password
```

## Directives:
```
--create_table: Builds the MySQL users table; no further action will be taken.

--file [csv_file_name]: Specifies the CSV file to be parsed.
--dry_run: Runs the script without altering the database.

-u [mysql_username]: MySQL username.
-p [mysql_password]: MySQL password.
-h [mysql_host]: MySQL host.
--help: Displays a list of directives with details.
```

## Logic Test

The logic test answer is attached in a file named foobar.php.

To execute the logic test, run:

```
php foobar.php
```

## AI Utilization

In the completion of this PHP task, I have harnessed the power of AI to enhance various aspects of the code and its functionality. The following queries were employed with AI, and I have customized the code accordingly:

```
- As a experienced PHP developer,suggest me how can I improve this function further?

- What are the possible edge cases which can throw exceptions in this function?

- How can I improve this CSV file reading function further?
```

