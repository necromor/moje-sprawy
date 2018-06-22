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

    public function pobierzLiczbeDecyzjiWRamachJrwa($jrwa, $rok) {
      /*
       * Pobiera liczbę decyzji w ramach podanego jrwa utworzonych w danym roku.
       *
       * Parametry:
       *  - jrwa => id numeru jrwa
       *  - rok => rok utworzenia decyji
       * Zwraca:
       *  - int => liczba decyzji spełniających kryteria
       */

      $sql = "SELECT COUNT(id) AS liczba FROM decyzje
                   WHERE id_jrwa=:id_jrwa AND utworzone LIKE :rok";
      $this->db->query($sql);
      $this->db->bind(':id_jrwa', $jrwa);
      $this->db->bind(':rok', $rok . "%");
      $row = $this->db->single();
      return $row->liczba;
    }

    public function pobierzDecyzje($rok, $jrwa) {
      /*
       * Pobiera wszystkie dane o decyzjach wystawionych w danym roku.
       *
       * Parametr jrwa to numer jrwa a nie jego id - prościej w adresie i formularzu
       *
       * Parametry:
       *  - rok => rok wystawienia decyzji
       *  - jrwa => numer jrwa (nie id)
       * Zwraca:
       *  - set zawierający obiekty decyzji; set jest posortowany rosnąco po znaku sprawy i dacie decyzji
       */

      // użyj wildcard jeżeli nie wybrano jrwa
      if ($jrwa == '') {
        $jrwa="%";
      }

      $sql = "SELECT wychodzace.*,
                     decyzje.id AS decyzjaId,
                     decyzje.numer AS decyzjaNumer,
                     decyzje.dotyczy AS decyzjaDotyczy,
                     decyzje.utworzone AS decyzjaData,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy, decyzje, jrwa
                     WHERE decyzje.utworzone LIKE :rok
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id
                       AND wychodzace.id=decyzje.id_wychodzace
                       AND decyzje.id_jrwa=jrwa.id
                       AND jrwa.numer LIKE :jrwa
                     ORDER BY sprawy.znak, decyzje.utworzone ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . "%");
      $this->db->bind(':jrwa', $jrwa);

      return $this->db->resultSet();
    }


  }


?>
