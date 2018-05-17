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

    public function pobierzPracownikow() {

     $sql = "SELECT id, imie, nazwisko FROM pracownicy WHERE aktywny=1 ORDER BY nazwisko ASC";
     $this->db->query($sql);

     return $this->db->resultSet(); 
    }
  
    public function pobierzWszystkichPracownikow() {

     $sql = "SELECT * FROM pracownicy ORDER BY id ASC";
     $this->db->query($sql);

     return $this->db->resultSet(); 
    }

    public function czyIstniejeLogin($login) {

      $sql = "SELECT id FROM pracownicy WHERE login=:login";
      $this->db->query($sql);
      $this->db->bind(':login', $login);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return false;
      } else {
        return true;
      }
      
    }

    public function dodajPracownika($data) {

      // wartości stałe
      $aktywny = 1; // nowozarejestrowany jest aktywny
      $zmiana_hasla = Date("Y-m-d H:i:s");

      $sql = "INSERT INTO pracownicy (imie, nazwisko, login, haslo, zmiana_hasla, poziom, aktywny) VALUES (:imie, :nazwisko, :login, :haslo, :zmiana_hasla, :poziom, :aktywny)";
      $this->db->query($sql);
      $this->db->bind(':imie', $data['imie']);
      $this->db->bind(':nazwisko', $data['nazwisko']);
      $this->db->bind(':login', $data['login']);
      $this->db->bind(':haslo', $data['haslo']);
      $this->db->bind(':zmiana_hasla', $zmiana_hasla);
      $this->db->bind(':poziom', $data['poziom']);
      $this->db->bind(':aktywny', $aktywny);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }



  }
