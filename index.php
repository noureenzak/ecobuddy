<?php
// Start the session
session_start();

$view = new stdClass();
// Set the page title for the view
$view->pageTitle = "Welcome to ecoBuddy";
// Including the index.phtml view file to render the homepage
require_once 'Views/index.phtml';
?>