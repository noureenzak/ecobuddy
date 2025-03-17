<?php
namespace Models;
/**
 * This class handles the database connection using the Singleton pattern.
 * It ensures only one instance of the database connection exists.
 */

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $_dbHandle;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
        $dbPath = __DIR__ . "/../ecobuddy.sqlite";

        if (!file_exists($dbPath)) {
            die("Database file not found at: " . $dbPath);
        }

        try {
            $this->_dbHandle = new PDO("sqlite:" . $dbPath);
            $this->_dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
    /**
     * Get the singleton instance of the Database class.
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * Get the database connection handle.
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_dbHandle;
    }
}
?>