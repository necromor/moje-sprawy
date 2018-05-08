<?php

  class Pracownik {

    private $db;

    public function __construct() {
      $this->db = new Database;
    }

    public function pobierzImieNazwisko($id) {

      $sql = "SELECT imie, nazwisko FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row->imie . ' ' . $row->nazwisko;
    }
  
  }
