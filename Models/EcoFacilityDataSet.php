<?php
namespace Models;

use PDO;
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/EcoFacilityStatus.php';
/**
 * Class EcoFacilityDataSet
 *
 *  This class handles database operations related to eco-friendly facilities.
 * it handles crud for admins and browsing users, so it has methods for retrieving, searching,
 * creating, updating and deleting
 */

class EcoFacilityDataSet
{
    private $_dbHandle;
    /**
     * Constructor for EcoFacilityDataSet.
     * Initializes the database connection using the Database singleton instance.
     */

    public function __construct()
    {
        $this->_dbHandle = Database::getInstance()->getConnection();
    }

    //retrieve all facilities from database
    public function getAllFacilities()
    {
        $sql = "
        SELECT f.id, f.title, c.name AS category, f.description, f.houseNumber,
               f.streetName, f.county, f.town, f.postcode, f.lng, f.lat,
               u.username AS contributor, 
               (SELECT s.statusComment 
                FROM ecoFacilityStatus s 
                WHERE s.facilityId = f.id 
                ORDER BY s.id DESC 
                LIMIT 1) AS statusComment
        FROM ecoFacilities f
        LEFT JOIN ecoCategories c ON f.category = c.id
        LEFT JOIN ecoUser u ON f.contributor = u.id
    ";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //searches the facility based on filter
    public function searchFacilities($filter, $query, $sort, $limit, $offset)
    {
        $allowedFilters = ['title', 'description', 'category', 'county', 'town', 'postcode', 'streetName'];
        if ($filter !== 'all' && !in_array($filter, $allowedFilters)) {
            throw new \Exception("Invalid filter: $filter");
        }

        $sql = "
        SELECT f.id, f.title, c.name AS category, f.description, f.houseNumber,
               f.streetName, f.county, f.town, f.postcode, f.lng, f.lat,
               u.username AS contributor, 
               (SELECT s.statusComment 
                FROM ecoFacilityStatus s 
                WHERE s.facilityId = f.id 
                ORDER BY s.id DESC 
                LIMIT 1) AS statusComment
        FROM ecoFacilities f
        LEFT JOIN ecoCategories c ON f.category = c.id
        LEFT JOIN ecoUser u ON f.contributor = u.id
    ";
        //add condition based on filter
        $conditions = [];
        $params = [];

        if ($filter !== 'all') {
            $conditions[] = "f.$filter LIKE :query";
            $params[':query'] = "%$query%";
        } else {
            $conditions[] = "(f.title LIKE :query OR f.description LIKE :query OR c.name LIKE :query OR f.county LIKE :query OR f.town LIKE :query OR f.postcode LIKE :query OR f.streetName LIKE :query OR u.username LIKE :query)";
            $params[':query'] = "%$query%";
        }
        //append conditions to the sql query
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        //define sorting options
        $sortOptions = [
            'none' => 'f.id ASC',
            'title_asc' => 'f.title ASC',
            'title_desc' => 'f.title DESC',
            'town_asc' => 'f.town ASC',
            'town_desc' => 'f.town DESC',
            'county_asc' => 'f.county ASC',
            'county_desc' => 'f.county DESC',
            'postcode_asc' => 'f.postcode ASC',
            'postcode_desc' => 'f.postcode DESC',
            'category_asc' => 'c.name ASC',
            'category_desc' => 'c.name DESC',
            'contributor_asc' => 'u.username ASC',
            'contributor_desc' => 'u.username DESC',
            'status_asc' => 'statusComment ASC',
            'status_desc' => 'statusComment DESC',
        ];
        $sortOrder = $sortOptions[$sort] ?? 'f.id ASC';
        $sql .= " ORDER BY $sortOrder";

        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->_dbHandle->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //retrieves facilities with pagination and sorting
    public function getFacilitiesWithPagination($sort, $limit, $offset)
    {
        $sortOptions = [
            'none' => 'f.id ASC',
            'title_asc' => 'f.title ASC',
            'title_desc' => 'f.title DESC',
            'town_asc' => 'f.town ASC',
            'town_desc' => 'f.town DESC',
            'county_asc' => 'f.county ASC',
            'county_desc' => 'f.county DESC',
            'postcode_asc' => 'f.postcode ASC',
            'postcode_desc' => 'f.postcode DESC',
            'category_asc' => 'c.name ASC',
            'category_desc' => 'c.name DESC',
            'contributor_asc' => 'u.username ASC',
            'contributor_desc' => 'u.username DESC',
            'status_asc' => 'statusComment ASC',
            'status_desc' => 'statusComment DESC',
        ];
        $sortOrder = $sortOptions[$sort] ?? 'f.id ASC';

        $sql = "
        SELECT f.id, f.title, c.name AS category, f.description, f.houseNumber,
               f.streetName, f.county, f.town, f.postcode, f.lng, f.lat,
               u.username AS contributor, 
               (SELECT s.statusComment 
                FROM ecoFacilityStatus s 
                WHERE s.facilityId = f.id 
                ORDER BY s.id DESC 
                LIMIT 1) AS statusComment
        FROM ecoFacilities f
        LEFT JOIN ecoCategories c ON f.category = c.id
        LEFT JOIN ecoUser u ON f.contributor = u.id
        ORDER BY $sortOrder
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //count search result

    public function countSearchResults($filter, $query)
    {
        if ($filter === 'all') {
            return $this->countSearchAllColumns($query);
        }

        $allowedFilters = ['title', 'description', 'category', 'county', 'town', 'postcode', 'streetName'];
        if (!in_array($filter, $allowedFilters)) {
            throw new \Exception("Invalid filter: $filter");
        }

        $stmt = $this->_dbHandle->prepare("
            SELECT COUNT(*) FROM ecoFacilities f
            LEFT JOIN ecoCategories c ON f.category = c.id
            WHERE f.$filter LIKE :query
        ");
        $searchTerm = "%$query%";
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    //count search of all columns
    public function countSearchAllColumns($query)
    {
        $stmt = $this->_dbHandle->prepare("
            SELECT COUNT(*) FROM ecoFacilities f
            LEFT JOIN ecoCategories c ON f.category = c.id
            WHERE f.title LIKE :query
               OR f.description LIKE :query
               OR c.name LIKE :query
               OR f.county LIKE :query
               OR f.town LIKE :query
               OR f.postcode LIKE :query
               OR f.streetName LIKE :query
        ");
        $searchTerm = "%$query%";
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    //count all facilities
    public function countAllFacilities()
    {
        $stmt = $this->_dbHandle->query("SELECT COUNT(*) FROM ecoFacilities");
        return $stmt->fetchColumn();
    }

    //for sorting
    public function getFacilityById($id)
    {
        $stmt = $this->_dbHandle->prepare("SELECT * FROM ecoFacilities WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //delete facility used when user clicks on delete
    public function deleteFacility($id)
    {
        $stmt = $this->_dbHandle->prepare("DELETE FROM ecoFacilities WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    //updates facility used when user clicks on edits
    public function updateFacility($data)
    {
        try {
            $query = "
        UPDATE ecoFacilities
        SET title = :title,
            category = :category,
            description = :description,
            houseNumber = :houseNumber,
            streetName = :streetName,
            county = :county,
            town = :town,
            postcode = :postcode,
            lng = :lng,
            lat = :lat,
            contributor = :contributor
        WHERE id = :id
        ";
            $stmt = $this->_dbHandle->prepare($query);

            $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':houseNumber', $data['houseNumber']);
            $stmt->bindParam(':streetName', $data['streetName']);
            $stmt->bindParam(':county', $data['county']);
            $stmt->bindParam(':town', $data['town']);
            $stmt->bindParam(':postcode', $data['postcode']);
            $stmt->bindParam(':lng', $data['lng']);
            $stmt->bindParam(':lat', $data['lat']);
            $stmt->bindParam(':contributor', $data['contributor'], PDO::PARAM_INT);

            $stmt->execute();

            $statusComment = $data['statusComment'];
            if ($statusComment) {
                $ecoFacilityStatus = new EcoFacilityStatus();
                $ecoFacilityStatus->updateStatusComment($data['id'], $statusComment);
            }

            return true;
        } catch (PDOException $e) {
            die("Error updating facility: " . $e->getMessage());
        }
    }

    //craete new facility
    public function createFacility($data)
    {
        try {
            // Check if a facility with the same title, postcode, longitude, and latitude already exists
            $stmt = $this->_dbHandle->prepare("
            SELECT id FROM ecoFacilities 
            WHERE title = :title AND postcode = :postcode AND lng = :lng AND lat = :lat
        ");
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':postcode', $data['postcode']);
            $stmt->bindParam(':lng', $data['lng']);
            $stmt->bindParam(':lat', $data['lat']);
            $stmt->execute();
            $existingFacility = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingFacility) {
                throw new \Exception("Facility with the same title, postcode, longitude, and latitude already exists.");
            }

            // Insert the new facility
            $query = "
            INSERT INTO ecoFacilities (
                title, category, description, houseNumber, streetName,
                county, town, postcode, lng, lat, contributor
            ) VALUES (
                :title, :category, :description, :houseNumber, :streetName,
                :county, :town, :postcode, :lng, :lat, :contributor
            )
        ";
            $stmt = $this->_dbHandle->prepare($query);

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':houseNumber', $data['houseNumber']);
            $stmt->bindParam(':streetName', $data['streetName']);
            $stmt->bindParam(':county', $data['county']);
            $stmt->bindParam(':town', $data['town']);
            $stmt->bindParam(':postcode', $data['postcode']);
            $stmt->bindParam(':lng', $data['lng']);
            $stmt->bindParam(':lat', $data['lat']);
            $stmt->bindParam(':contributor', $data['contributor'], PDO::PARAM_INT);

            $stmt->execute();

            // Add status comment if provided
            $statusComment = $data['statusComment'] ?? null;
            if ($statusComment) {
                $facilityId = $this->_dbHandle->lastInsertId();
                $ecoFacilityStatus = new EcoFacilityStatus();
                $ecoFacilityStatus->addStatusComment($facilityId, $statusComment); // Only 2 arguments
            }

            return true;
        } catch (PDOException $e) {
            throw new \Exception("Error creating facility: " . $e->getMessage());
        }
    }
    //updates status comment for edit by browing users

    public function updateFacilityStatus($facilityId, $statusComment)
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

        if (!in_array($statusComment, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status comment.");
        }

        $stmt = $this->_dbHandle->prepare("
        UPDATE ecoFacilityStatus
        SET statusComment = :statusComment
        WHERE facilityId = :facilityId
    ");
        $stmt->bindParam(':facilityId', $facilityId, PDO::PARAM_INT);
        $stmt->bindParam(':statusComment', $statusComment, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
