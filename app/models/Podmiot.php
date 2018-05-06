<?php

  class Podmiot {

    private $db;

    public function __construct() {
      $this->db = new Database;
    }

    public function dodajPodmiot($data) {

      $sql = "INSERT INTO podmioty (nazwa, adres_1, adres_2) VALUES (:nazwa, :adres_1, :adres_2)";
      $this->db->query($sql);
      $this->db->bind(':nazwa', $data['nazwa_podmiotu']);
      $this->db->bind(':adres_1', $data['adres_podmiotu']);
      $this->db->bind(':adres_2', $data['poczta_podmiotu']);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

   public function pobierzPodmioty() {

     $sql = "SELECT * FROM podmioty ORDER BY id ASC";
     $this->db->query($sql);

     return $this->db->resultSet(); 
   }



  }
