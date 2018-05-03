<?php

  class Nadawca {

    private $db;

    public function __construct() {
      $this->db = new Database;
    }

    public function dodajNadawce($data) {

      $sql = "INSERT INTO nadawcy (nazwa, adres_1, adres_2) VALUES (:nazwa, :adres_1, :adres_2)";
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



  }
