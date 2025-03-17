<?php
session_start();
require_once 'Models/EcoFacilityDataSet.php';
use Models\EcoFacilityDataSet;

$ecoFacility = new EcoFacilityDataSet();
$id = $_GET['id'] ?? null;
$error = '';
$facility = null;

// Validate the facility ID
if (!$id) {
    die('Error: No facility ID provided.');
}

// Fetch facility data by ID
$facility = $ecoFacility->getFacilityById($id);

// If facility not found, stop the process
if (!$facility) {
    die('Error: Facility with ID ' . htmlspecialchars($id) . ' not found.');
}

// Handle form submission for updating the facility
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
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
        'contributor' => htmlspecialchars($_POST['contributor']),
        'statusComment' => htmlspecialchars($_POST['statusComment'])
    ];

    // Ensure required fields are filled
    if (
        !empty($data['title']) &&
        !empty($data['category']) &&
        !empty($data['postcode']) &&
        !empty($data['lng']) &&
        !empty($data['lat']) &&
        !empty($data['statusComment']) &&
        !empty($data['contributor'])
    ) {
        // Update the facility in the database
        if ($ecoFacility->updateFacility($data)) {
            $_SESSION['success'] = "Facility updated successfully.";
            header('Location: crud.php');
            exit;
        } else {
            $error = "Failed to update facility. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Facility</title>
    <link rel="stylesheet" href="Views/css/style.css">
</head>
<body>
<div class="form-container">
    <h1>Edit Facility</h1>

    <!-- Display error messages -->
    <?php if (!empty($error)): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <form method="post" action="editFacility.php?id=<?= htmlspecialchars($id); ?>">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($facility['title'] ?? ''); ?>" required>

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($facility['category'] ?? ''); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($facility['description'] ?? ''); ?></textarea>

        <label>House Number:</label>
        <input type="text" name="houseNumber" value="<?= htmlspecialchars($facility['houseNumber'] ?? ''); ?>">

        <label>Street Name:</label>
        <input type="text" name="streetName" value="<?= htmlspecialchars($facility['streetName'] ?? ''); ?>">

        <label>County:</label>
        <input type="text" name="county" value="<?= htmlspecialchars($facility['county'] ?? ''); ?>">

        <label>Town:</label>
        <input type="text" name="town" value="<?= htmlspecialchars($facility['town'] ?? ''); ?>">

        <label>Postcode:</label>
        <input type="text" name="postcode" value="<?= htmlspecialchars($facility['postcode'] ?? ''); ?>" required>

        <label>Longitude:</label>
        <input type="text" name="lng" value="<?= htmlspecialchars($facility['lng'] ?? ''); ?>" required>

        <label>Latitude:</label>
        <input type="text" name="lat" value="<?= htmlspecialchars($facility['lat'] ?? ''); ?>" required>

        <label>Contributor:</label>
        <input type="number" name="contributor" value="<?= htmlspecialchars($facility['contributor'] ?? ''); ?>" required>

        <label>Status:</label>
        <select name="statusComment" required>
            <option value="Bin is full" <?= ($facility['statusComment'] ?? '') === 'Bin is full' ? 'selected' : ''; ?>>Bin is full</option>
            <option value="Not working" <?= ($facility['statusComment'] ?? '') === 'Not working' ? 'selected' : ''; ?>>Not working</option>
            <option value="Often busy" <?= ($facility['statusComment'] ?? '') === 'Often busy' ? 'selected' : ''; ?>>Often busy</option>
            <option value="One charger not working" <?= ($facility['statusComment'] ?? '') === 'One charger not working' ? 'selected' : ''; ?>>One charger not working</option>
            <option value="Always lots available" <?= ($facility['statusComment'] ?? '') === 'Always lots available' ? 'selected' : ''; ?>>Always lots available</option>
            <option value="Great way to get around" <?= ($facility['statusComment'] ?? '') === 'Great way to get around' ? 'selected' : ''; ?>>Great way to get around</option>
            <option value="Great to charge your phone but bring a cable" <?= ($facility['statusComment'] ?? '') === 'Great to charge your phone but bring a cable' ? 'selected' : ''; ?>>Great to charge your phone but bring a cable</option>
            <option value="Pending" <?= ($facility['statusComment'] ?? '') === 'Pending' ? 'selected' : ''; ?>>Pending</option>
        </select>

        <button type="submit" class="btn-primary">Update Facility</button>
    </form>

    <div class="top-actions">
        <a href="crud.php" class="back-button">Back to Facilities</a>
    </div>
</div>
</body>
</html>