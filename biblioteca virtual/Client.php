<?php
class Client {
    private $id;
    private $email;
    private $password;
    private $name;
    private $lastName;
    private $address;
    private $colonia;
    private $city;
    private $state;
    private $country;
    private $postalCode;
    private $phone;
    private $tipo;

    function __construct($email = "", $password = ""){
        $this->id = 0;
        $this->email = $email;
        $this->password = $password;
        $this->name = "";
        $this->lastName = "";
        $this->address = "";
        $this->colonia = "";
        $this->city = "";
        $this->state = "";
        $this->country = "";
        $this->postalCode = "";
        $this->phone = "";
        $this->tipo = "Cliente";
    }

    public function setId($newId){
        $this->id = $newId;
    }

    public function getId(){
        return $this->id;
    }

    public function setEmail($newEmail){
        $this->email = $newEmail;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setPassword($newPassword){
        $this->password = $newPassword;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setColonia($colonia) {
        $this->colonia = $colonia;
    }

    public function getColonia() {
        return $this->colonia;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getCity() {
        return $this->city;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getState() {
        return $this->state;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    public function getTipo() {
        return $this->tipo;
    }
    
    public function isAdmin() {
        return $this->tipo === 'Administrador';
    }
}
?>