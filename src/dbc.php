<?php
include_once 'config.php';

class Dbc {
    private $user, $password, $db_name, $host;

    public function __construct() {
        // Access the variables directly from the included file
        global $user, $password, $db_name, $host;

        // Assign the values to class properties
        $this->user = $user;
        $this->password = $password;
        $this->db_name = $db_name;
        $this->host = $host;
    }

    public function dbc() {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $dbc = new mysqli($this->host, $this->user, $this->password, $this->db_name);
            $dbc->set_charset("utf8mb4");
            return $dbc;
        } catch (Exception $e) {
            die('Database connection failed: ' . $e->getMessage() . "\n");
        }
    }

    // For making database connection parameters available
    public function db_params() {
        return array('user' => $this->user, 'pass' => $this->password, 'db' => $this->db_name, 'host' => $this->host);
    }
}
?>
