<?php
namespace Models;

use PDO;

require_once __DIR__ . '/Database.php';
/**
 * Class EcoUserDataSet
 *
 * This class handles all database operations related to users in the ecoBuddy system.
 * It provides methods for:
 * - Finding users by username.
 * - Authenticating users during login.
 * - Creating, updating, and deleting users.
 * - Fetching paginated lists of users.
 * - Managing user passwords.
 *
 * The class uses the Database singleton to establish a connection and execute queries.
 * It ensures secure password handling by hashing passwords before storing them in the database.
 */
class EcoUserDataSet
{
    private $_dbHandle;
    // Constructor: Initializes the database connection
    public function __construct()
    {
        $this->_dbHandle = Database::getInstance()->getConnection();
    }
    // find a user by username
    public function findUserByUsername($username)
    {
        $stmt = $this->_dbHandle->prepare("SELECT * FROM ecoUser WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Print the fetched user data
        echo "<pre>";
        print_r($user);
        echo "</pre>";

        return $user;  // Returns user data or false if not found
    }

    // authenticate a user by username and password for login
    public function authenticate($username, $password)
    {
        $user = $this->findUserByUsername($username);

        // Debug: Print the user data and entered password
        echo "<pre>";
        echo "User Data: ";
        print_r($user);
        echo "Entered Password: " . $password . "\n";
        echo "</pre>";

        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'userType' => $user['userType'],
                'password' => $user['password']  // Include password in the returned array for debugging
            ];
        }
        return false;
    }
    // create a new user (used by admins and alos in testing)
    public function createUser($username, $password, $userType)
    {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->_dbHandle->prepare("
        INSERT INTO ecoUser (username, password, userType)
        VALUES (:username, :password, :userType)
    ");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':userType', $userType, PDO::PARAM_STR);

        return $stmt->execute();
    }
    // update a user's password
    public function updatePassword($username, $newPassword)
    {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->_dbHandle->prepare("UPDATE ecoUser SET password = :password WHERE username = :username");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        return $stmt->execute();
    }

    // tofetch paginated users

    public function fetchUsersPaginated($offset, $itemsPerPage)
{
    $query = "SELECT * FROM ecoUser LIMIT :offset, :itemsPerPage";
    $stmt = $this->_dbHandle->prepare($query);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // Get the total number of users
    public function getTotalUsersCount()
    {
        $query = "SELECT COUNT(*) FROM ecoUser";
        $stmt = $this->_dbHandle->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>