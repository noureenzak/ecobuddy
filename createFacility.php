<?php
session_start();
require_once 'Models/EcoFacilityDataSet.php';
use Models\EcoFacilityDataSet;

$ecoFacility = new EcoFacilityDataSet();
$error = '';
$successMessage = '';

// Redirect if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'title' => htmlspecialchars($_POST['title']),
            'category' => htmlspecialchars($_POST['category']),
            'description' => htmlspecialchars($_POST['description']),
            'houseNumber' => htmlspecialchars($_POST['houseNumber']),
            'streetName' => htmlspecialchars($_POST['streetName']),
            'county' => htmlspecialchars($_POST['county']),
            'town' => htmlspecialchars($_POST['town']),
            'postcode' => htmlspecialchars($_POST['postcode']),
            'lng' => htmlspecialchars($_POST['lng']),
            'lat' => htmlspecialchars($_POST['lat']),
            'contributor' => $_SESSION['user_id'], // Contributor is the logged-in user
            'statusComment' => htmlspecialchars($_POST['statusComment']) // Ensure status is passed
        ];

        // Attempt to create the facility
        if ($ecoFacility->createFacility($data)) {
            $_SESSION['success'] = "Facility added successfully.";
            header('Location: crud.php');
            exit;
        }
    } catch (Exception $e) {
        // Catch the duplicate facility error and display it
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Facility</title>
    <link rel="stylesheet" href="Views/css/style.css">
</head>
<body>
<div class="form-container">
    <h1>Create New Facility</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Category:</label>
        <input type="text" name="category" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>House Number:</label>
        <input type="text" name="houseNumber">

        <label>Street Name:</label>
        <input type="text" name="streetName">

        <label>County:</label>
        <input type="text" name="county">

        <label>Town:</label>
        <input type="text" name="town">

        <label>Postcode:</label>
        <input type="text" name="postcode" required>

        <label>Longitude:</label>
        <input type="text" name="lng" required>

        <label>Latitude:</label>
        <input type="text" name="lat" required>

        <!-- Remove the contributor input field since it's tied to the logged-in user -->
        <input type="hidden" name="contributor" value="<?= $_SESSION['user_id']; ?>">

        <label>Status:</label>
        <select name="statusComment" required>
            <option value="Bin is full">Bin is full</option>
            <option value="Not working">Not working</option>
            <option value="Often busy">Often busy</option>
            <option value="One charger not working">One charger not working</option>
            <option value="Always lots available">Always lots available</option>
            <option value="Great way to get around">Great way to get around</option>
            <option value="Great to charge your phone but bring a cable">Great to charge your phone but bring a cable</option>
            <option value="Pending">Pending</option>
        </select>

        <button type="submit">Create Facility</button>
    </form>
</div>
</body>
</html>