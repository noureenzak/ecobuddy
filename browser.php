<?php
session_start();
require_once 'Models/EcoFacilityDataSet.php';

use Models\EcoFacilityDataSet;

$ecoFacility = new EcoFacilityDataSet();
$view = new stdClass();
$view->pageTitle = "Browse Eco-Friendly Facilities";

// Handle search and pagination
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all'; // Default filter is 'all'
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'title_asc'; // Default sorting
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
// Ensure the list of allowed statuses is passed to the view
$view->allowedStatuses = $ecoFacility->getAllowedStatuses();

$view->totalPages = ceil($view->totalFacilities / $limit);
$view->currentPage = $page;
$view->query = htmlspecialchars($q);
$view->filter = htmlspecialchars($filter);
$view->sort = htmlspecialchars($sort);

// Handle status update for browsing users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['role'] == 2) { // Ensure browsing users can update
        $facilityId = (int)$_POST['id'];
        $statusComment = htmlspecialchars($_POST['statusComment']);

        try {
            $ecoFacility->updateFacilityStatus($facilityId, $statusComment);
            $_SESSION['success'] = "Status updated successfully.";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error updating status: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "You do not have permission to update the status.";
    }
    header('Location: browser.php');
    exit;
}

require_once 'Views/browser.phtml';
?>