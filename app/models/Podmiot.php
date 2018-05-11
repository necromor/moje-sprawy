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

    public function edytujPodmiot($data) {

      $sql = "UPDATE podmioty SET nazwa=:nazwa, adres_1=:adres_1, adres_2=:adres_2 WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':nazwa', $data['nazwa_podmiotu']);
      $this->db->bind(':adres_1', $data['adres_podmiotu']);
      $this->db->bind(':adres_2', $data['poczta_podmiotu']);
      $this->db->bind(':id', $data['id']);

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

   public function pobierzDanePodmiotu($id) {

     $sql = "SELECT * FROM podmioty WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);
     
     return $this->db->single();
   }

   public function pobierzDanePodmiotuPoNazwie($nazwa) {

     // nazwa nie jest unikalna
     // pobierany jest ten najnowszy
     $sql = "SELECT * FROM podmioty WHERE nazwa=:nazwa ORDER BY id DESC LIMIT 1";
     $this->db->query($sql);
     $this->db->bind(':nazwa', $nazwa);
     
     return $this->db->single();
   }


  }
