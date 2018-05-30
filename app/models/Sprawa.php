<?php
  /*
   * Model Sprawa obsługuje wszystkie funkcje związane ze sprawami, takie jak:
   *  - dodanie nowej sprawy
   *  - przypisanie korespondencji do sprawy
   *  - usunięcie przypisanego pisma do sprawy
   *  - edycja tematu sprawy
   *
   * Dodatkowo model obsługuje funkcje związane z pobieraniem danych sprawy
   * na różne sposoby.
   *
   */

  class Sprawa {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

   public function pobierzSprawePoId($id) {
      /*
       * Pobiera wszystkie dane o wybranej sprawie na podstawie jej id.
       *
       * Parametry:
       *  - id => id szukanej sprawy
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane o sprawie
       */

     $sql = "SELECT * FROM sprawy WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);

     return $this->db->single();
   }

   public function pobierzSprawePoZnaku($znak) {
      /*
       * Pobiera wszystkie dane o wybranej sprawie na podstawie jej znaku.
       *
       * Parametry:
       *  - znak => znak szukanej sprawy
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane o sprawie
       */

     $sql = "SELECT * FROM sprawy WHERE znak=:znak";
     $this->db->query($sql);
     $this->db->bind(':znak', $znak);

     return $this->db->single();
   }

   public function dodajSprawe($znak, $jrwa, $temat) {
      /*
       * Dodaje nową sprawę do bazy danych.
       *
       * Parametry:
       *  - znak => znak sprawy do dodania
       *  - jrwa => numer jrwa sprawy do dodania
       *  - temat => temat sprawy do dodania
       * Zwraca:
       *  - boolean
       */

      $sql = "INSERT INTO sprawy (znak, temat, id_jrwa) VALUES (:znak, :temat, :id_jrwa)";
      $this->db->query($sql);
      $this->db->bind(':znak', $znak);
      $this->db->bind(':temat', $temat);
      $this->db->bind(':id_jrwa', $jrwa);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
   }

   public function pobierzLiczbeSpraw($jrwa, $rok) {
     /*
      * Pobiera liczbę spraw w ramach podanego jrwa i w danym roku.
      *
      * Parametry:
      *  - jrwa => id numeru jrwa
      *  - rok => rok w którym sprawa została utworzona
      * Zwraca:
      *  - int => liczba spraw
      */

      $sql = "SELECT COUNT(id) as total FROM sprawy WHERE id_jrwa=:jrwa AND utworzone LIKE :rok";
      $this->db->query($sql);
      $this->db->bind(':jrwa', $jrwa);
      $this->db->bind(':rok', $rok . '%');

      $row = $this->db->single();
      return intval($row->total);
   }



  }
