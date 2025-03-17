<?php
namespace Models;
/**
 * This class represents a single eco-facility record in the system.
 * It encapsulates all the properties and methods related to an eco-facility,
 * such as its title, description, location, and contributor.
 */
class EcoFacilityData
{
    // Properties representing the eco-facility's attributes
    private $id;
    private $title;
    private $category;
    private $description;
    private $houseNumber;
    private $streetName;
    private $county;
    private $town;
    private $postcode;
    private $lng;
    private $lat;
    private $contributor;
    private $statusComment;
    //constructor for the EcoFacilityDta class
    public function __construct($id, $title, $category, $description, $houseNumber, $streetName, $county, $town, $postcode, $lng, $lat, $contributor, $statusComment)
    {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->description = $description;
        $this->houseNumber = $houseNumber;
        $this->streetName = $streetName;
        $this->county = $county;
        $this->town = $town;
        $this->postcode = $postcode;
        $this->lng = $lng;
        $this->lat = $lat;
        $this->contributor = $contributor;
        $this->statusComment = $statusComment;
    }

    // Getters for each property
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getCategory() { return $this->category; }
    public function getDescription() { return $this->description; }
    public function getHouseNumber() { return $this->houseNumber; }
    public function getStreetName() { return $this->streetName; }
    public function getCounty() { return $this->county; }
    public function getTown() { return $this->town; }
    public function getPostcode() { return $this->postcode; }
    public function getLng() { return $this->lng; }
    public function getLat() { return $this->lat; }
    public function getContributor() { return $this->contributor; }
    public function getStatusComment() { return $this->statusComment; }
}
?>