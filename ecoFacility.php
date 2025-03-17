<?php
session_start();
// Include the EcoFacilityDataSet class file and use the namespace
require_once 'Models/EcoFacilityDataSet.php';
use Models\EcoFacilityDataSet;
// Create an instance of the EcoFacilityDataSet class
$ecoFacility = new EcoFacilityDataSet();

// Retrieve and sanitize search parameters from the query string
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$postcode = isset($_GET['postcode']) ? trim($_GET['postcode']) : '';
$address = isset($_GET['address']) ? trim($_GET['address']) : '';
// Set pagination parameters
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
// Check if any search parameters are provided
if (!empty($q) || !empty($category) || !empty($postcode) || !empty($address)) {
    // Perform a search for facilities based on the provided filters
    $facilities = $ecoFacility->searchFacilities($q, $category, $postcode, $address);
    $totalFacilities = $ecoFacility->countSearchResults($q, $category, $postcode, $address);
} else {
    // If no search parameters are provided, fetch all facilities with pagination
    $facilities = $ecoFacility->getFacilitiesWithPagination($limit, $offset);
    // Get the total number of facilities in the database
    $totalFacilities = $ecoFacility->countAllFacilities();
}

$totalPages = ceil($totalFacilities / $limit);
// Create a view object to pass data to the view
$view = new stdClass();
$view->facilities = $facilities;
$view->totalPages = $totalPages;
$view->currentPage = $page;
$view->query = htmlspecialchars($q);
$view->category = htmlspecialchars($category); // Ensure this is defined
$view->postcode = htmlspecialchars($postcode); // Ensure this is defined
$view->address = htmlspecialchars($address);   // Ensure this is defined

require_once 'Views/crud.phtml';
?>