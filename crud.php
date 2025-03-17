<?php
session_start();
require_once 'Models/EcoFacilityDataSet.php';
use Models\EcoFacilityDataSet;

$ecoFacility = new EcoFacilityDataSet();
// Create a view object to pass data to the view
$view = new stdClass();
$view->pageTitle = "Manage Facilities";

// Check if the user is logged in and their role
$view->isLoggedIn = isset($_SESSION['user_id']);
$view->isAdmin = $view->isLoggedIn && ($_SESSION['role'] === 'admin'); // Assuming 'role' is stored in the session

// Define allowed statuses for facility updates
$view->allowedStatuses = [
    'Bin is full',
    'Not working',
    'Often busy',
    'One charger not working',
    'Always lots available',
    'Great way to get around',
    'Great to charge your phone but bring a cable',
    'Pending'
];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if ($view->isLoggedIn && $_SESSION['role'] == 2) { // Ensure browsing users can update
        $facilityId = (int)$_POST['id'];
        $statusComment = trim($_POST['statusComment']);
        // Validate the status comment
        if (!in_array($statusComment, $view->allowedStatuses)) {
            $_SESSION['error'] = "Invalid status selected.";
        } else {
            try {
                $ecoFacility->updateFacilityStatus($facilityId, $statusComment);
                $_SESSION['success'] = "Status updated successfully.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Error updating status: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['error'] = "You do not have permission to update the status.";
    }
    header('Location: crud.php');
    exit;
}

// Handle search, sorting, and pagination
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all'; // Default filter is 'all'
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'none'; // Default sorting is 'none'
$limit = 10; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (!empty($q)) {
    // Search facilities based on the selected filter and sorting
    $view->facilities = $ecoFacility->searchFacilities($filter, $q, $sort, $limit, $offset);
    $view->totalFacilities = $ecoFacility->countSearchResults($filter, $q);
} else {
    // If no search query, fetch all facilities with pagination and sorting
    $view->facilities = $ecoFacility->getFacilitiesWithPagination($sort, $limit, $offset);
    $view->totalFacilities = $ecoFacility->countAllFacilities();
}
// Sanitize and store search-related data for the view
$view->totalPages = ceil($view->totalFacilities / $limit);
$view->currentPage = $page;
$view->query = htmlspecialchars($q);
$view->filter = htmlspecialchars($filter);
$view->sort = htmlspecialchars($sort);

// Handle delete operation (Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['role'] == 1) { // Check if user is admin
        $id = $_POST['id'];
        if ($ecoFacility->deleteFacility($id)) {
            $_SESSION['success'] = "Facility deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete facility.";
        }
    } // Redirect to the CRUD page to prevent form resubmission
    header('Location: crud.php');
    exit;
}

require_once 'Views/crud.phtml';
?>