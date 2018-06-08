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

   public function pobierzNumerySpraw($rok=-1, $jrwa=-1) {
      /*
       * Pobiera numery spraw na podstawie podanego roku i/ewentualnie numeru jrwa.
       *
       * Parametry:
       *  - rok => rok założenia spraw
       *  - jrwa => id numeru jrwa spraw do wyświetlenia
       * Zwraca:
       *  - set numerów spraw
       */

     if($rok == -1) {$rok = Date("Y");}

     if ($jrwa == -1) {
       $sql = "SELECT znak FROM sprawy WHERE utworzone LIKE :rok";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . "%");
     } else {
       $sql = "SELECT znak FROM sprawy WHERE utworzone LIKE :rok AND id_jrwa=:jrwa";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . "%");
       $this->db->bind(':jrwa', $jrwa);
     }

     return $this->db->resultSet();
   }

   public function zmienTemat($id, $temat) {
      /*
       * Zmienia temat istniejącej sprawy.
       *
       * Parametry:
       *  - id => id sprawy do zmiany
       *  - temat => nowy temat sprawy
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE sprawy SET temat=:temat WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':temat', $temat);

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

   public function czyZakonczona($id) {
      /*
       * Sprawdza czy sprawa jest zakończona.
       * Sprawa jest zakończona jeżeli istnieje data w bazie danych.
       *
       * Parametry:
       *  - id => id szukanej sprawy
       * Zwraca:
       *  - boolean => true jeżeli zakończona
       */

     $sql = "SELECT zakonczona FROM sprawy WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);

     $row = $this->db->single();

     return $row->zakonczona != NULL;
   }

   public function zakonczSprawe($id) {
      /*
       * Ustawia sprawę jako zakończoną.
       *
       * Parametry:
       *  - id => id sprawy do zakończenia
       * Zwraca:
       *  - boolean
       */

      $data = Date("Y-m-d H:i:s");

      $sql = "UPDATE sprawy SET zakonczona=:data WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':data', $data);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
   }

   public function wznowSprawe($id) {
      /*
       * Wznawia zakończoną sprawę.
       *
       * Parametry:
       *  - id => id sprawy do wznowienia
       * Zwraca:
       *  - boolean
       */

      $data = NULL;

      $sql = "UPDATE sprawy SET zakonczona=:data WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':data', $data);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
   }




  }
