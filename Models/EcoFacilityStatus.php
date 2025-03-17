<?php
namespace Models;
/**
 * Class EcoFacilityStatus
 *
 * This class handles operations related to the status comments of eco-friendly facilities.
 * It allows adding, updating, and retrieving status comments for facilities, and ensures
 * that only valid status comments are used.
 */
use PDO;

class EcoFacilityStatus
{
    private $_dbHandle;
    /**
     * Constructor for EcoFacilityStatus.
     * Initializes the database connection using the Database singleton instance.
     */
    public function __construct()
    {
        $this->_dbHandle = Database::getInstance()->getConnection();
    }

    // Validate status comment
    private function isValidStatus($statusComment)
    {
        $allowedStatuses = [
            'Bin is full',
            'Not working',
            'Often busy',
            'One charger not working',
            'Always lots available',
            'Great way to get around',
            'Great to charge your phone but bring a cable',
            'Pending'
        ];

        return in_array($statusComment, $allowedStatuses);
    }

    // Add a status comment for a facility
    public function addStatusComment($facilityId, $statusComment)
    {
        try {
            $query = "
                INSERT INTO ecoFacilityStatus (facilityId, statusComment)
                VALUES (:facilityId, :statusComment)
            ";
            $stmt = $this->_dbHandle->prepare($query);
            $stmt->bindParam(':facilityId', $facilityId, PDO::PARAM_INT);
            $stmt->bindParam(':statusComment', $statusComment, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Error adding status comment: " . $e->getMessage());
        }
    }

    // Get the latest status comment for a facility
    public function getStatusComment($facilityId)
    {
        $stmt = $this->_dbHandle->prepare("
            SELECT statusComment FROM ecoFacilityStatus
            WHERE facilityId = :facilityId
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->bindParam(':facilityId', $facilityId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['statusComment'] : null;
    }

    // Update the status comment for a facility
    public function updateStatusComment($facilityId, $statusComment)
    {
        if (!$this->isValidStatus($statusComment)) {
            throw new \InvalidArgumentException("Invalid status comment: $statusComment");
        }

        $stmt = $this->_dbHandle->prepare("
            UPDATE ecoFacilityStatus
            SET statusComment = :statusComment
            WHERE facilityId = :facilityId
        ");
        $stmt->bindParam(':facilityId', $facilityId, PDO::PARAM_INT);
        $stmt->bindParam(':statusComment', $statusComment, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new \Exception("No facility found with ID: $facilityId");
        }

        return true;
    }

    // Get all allowed statuses
    public function getAllowedStatuses()
    {
        return [
            'Bin is full',
            'Not working',
            'Often busy',
            'One charger not working',
            'Always lots available',
            'Great way to get around',
            'Great to charge your phone but bring a cable',
            'Pending'
        ];
    }
}
?>