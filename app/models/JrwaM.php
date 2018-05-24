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


  }
