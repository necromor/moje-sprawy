<?php
  /*
   * Model JrwaM obsługuje wszystkie funkcje związane z numerami JRWA, takie jak:
   * - dodanie nowego - pojedynczy lub grupa
   * - edycja istniejącego
   *
   * Wyjątkowo odejście od zasady, że model to liczba pojedyncza kontrolera.
   * Kontroler jest w liczbie pojedynczej i powinien taki zostać ze względu
   * na adresy url.
   *
   */

  class JrwaM {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function dodajJrwa($numer, $opis) {
      /*
       * Dodaje nowy numer jrwa do bazy danych.
       *
       * Parametry:
       *  - numer => numer jrwa do dodania
       *  - opis => opis numeru jrwa do dodania
       * Zwraca:
       *  - boolean
       */

      $sql = "INSERT INTO jrwa (numer, opis) VALUES (:numer, :opis)";
      $this->db->query($sql);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':opis', $opis);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function edytujJrwa($numer, $opis, $id) {
      /*
       * Edytuje dane numeru jrwa.
       * Dla uproszczenia procesu zmieniane są wszystkie dane
       * bez sprawdzania, które faktycznie są inne.
       *
       * Parametry:
       *  - numer => numer jrwa do dodania
       *  - opis => opis numeru jrwa do dodania
       *  - id => id zmienianego numeru
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE jrwa SET numer=:numer, opis=:opis WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':opis', $opis);
      $this->db->bind(':id', $id);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function pobierzJrwa() {
      /*
       * Pobiera listę numerów jrwa posortowanych rosnąco po numerze.
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - set zawierający dane numerów jrwa
       */

     $sql = "SELECT * FROM jrwa ORDER BY numer ASC";
     $this->db->query($sql);

     return $this->db->resultSet();
    }

    public function pobierzJrwaPoId($id) {
      /*
       * Pobiera dane numeru jrwa po jego id.
       *
       * Parametry:
       *  - id => id szukanego numeru
       * Zwraca:
       *  - wiersz z tabeli zawierające dane obiektu
       */

     $sql = "SELECT * FROM jrwa WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);


     return $this->db->single();
    }

    public function pobierzJrwaPoNumerze($numer) {
      /*
       * Pobiera dane numeru jrwa po jego numerze.
       *
       * Parametry:
       *  - numer => numer szukanego jrwa
       * Zwraca:
       *  - wiersz z tabeli zawierające dane obiektu
       */

     $sql = "SELECT * FROM jrwa WHERE numer=:numer";
     $this->db->query($sql);
     $this->db->bind(':numer', $numer);


     return $this->db->single();
    }

    public function czyIstniejeJrwa($numer, $id) {
      /*
       * Sprawdza czy w bazie danych istnieje podany numer jrwa, który
       * ma id różne od podanego.
       *
       * Parametry:
       *  - numer => numer szukanego jrwa
       *  - id => id numeru do wykluczenia z poszukiwań
       * Zwraca:
       *  - boolean => true jeżeli istnieje
       */

      $sql = "SELECT id FROM jrwa WHERE numer=:numer AND id!=:id";
      $this->db->query($sql);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return false;
      } else {
        return true;
      }
    }


  }
