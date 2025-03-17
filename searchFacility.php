<?php
session_start();
require_once 'Models/EcoFacilityDataSet.php';
use Models\EcoFacilityDataSet;

$ecoFacility = new EcoFacilityDataSet();
$view = new stdClass();
$view->pageTitle = "Search Eco-Friendly Facilities";

// Handle search, sorting, and pagination
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all'; // Default filter is 'all'
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'sort'; // Default sorting is 'none'
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

$view->totalPages = ceil($view->totalFacilities / $limit);
$view->currentPage = $page;
$view->query = htmlspecialchars($q);
$view->filter = htmlspecialchars($filter);
$view->sort = htmlspecialchars($sort);

require_once 'Views/searchFacility.phtml';
?>