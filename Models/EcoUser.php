<?php
namespace Models;
/**
 * Class EcoUser
 *
 * This class represents a user in the ecoBuddy system.
 * It stores user details such as ID, username, password, and user type.
 * It also provides getter methods to access these properties.
 */
class EcoUser
{
    // Private properties to store user details
    private $id;
    private $username;
    private $password;
    private $userType;

    //construxtor for EcoUser
    public function __construct($id, $username, $password, $userType)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->userType = $userType;
    }

    // Getters for each property
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getUserType() { return $this->userType; }
}
?>