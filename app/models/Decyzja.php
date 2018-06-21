<?php
  /*
   * Model Decyzja obsługuje wszystkie funkcje związane
   * z decyzjami.
   * Funkcje te obejmują:
   *  - dodawanie nowej decyzji przy dodawaniu pisma wychodzącego
   *  - edycję istniejącej decyzji - znak i dotyczy
   *  - tworzenie zestawień decyzji w podziale na rok i/lub jrwa
   *
   *  Decyzja stanowi całość dopiero w połączeniu z pismem wychodzącym.
   *  Samodzielnie nie może istnieć - brak sprawy, brak odbiorcy.
   *
   */

  class Decyzja {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function dodajDecyzje($id_wychodzace, $numer, $dotyczy, $id_jrwa) {
      /*
       * Dodaje decyzję do bazy danych.
       *
       * Parametry:
       *  - id_wychodzace => id pisma wychodzącego, do którego decyzja jest przypisana
       *  - numer => treść pola numer decyzji
       *  - dotyczy => treść pola dotyczy decyzji
       *  - id_jrwa => id numeru jrwa sprawy w ramach której dodawana jest decyzja
       * Zwraca:
       *  - obiekt dodanej decyzji
       */

      $sql = "INSERT INTO decyzje (id_wychodzace, numer, dotyczy, id_jrwa)
              VALUES (:id_wychodzace, :numer, :dotyczy, :id_jrwa)";
      $this->db->query($sql);
      $this->db->bind(':id_wychodzace', $id_wychodzace);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':dotyczy', $dotyczy);
      $this->db->bind(':id_jrwa', $id_jrwa);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      if ($this->db->execute()) {
        $sql = "SELECT * FROM decyzje
                  WHERE id_wychodzace=:id_wychodzace
                    AND numer=:numer
                    AND dotyczy=:dotyczy
                    AND id_jrwa=:id_jrwa
                  ORDER BY utworzone DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id_wychodzace', $id_wychodzace);
        $this->db->bind(':numer', $numer);
        $this->db->bind(':dotyczy', $dotyczy);
        $this->db->bind(':id_jrwa', $id_jrwa);
        $row = $this->db->single();
        return $row;
      }
    }



  }


?>
