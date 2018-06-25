<?php
  /*
   * Model Postanowienie obsługuje wszystkie funkcje związane
   * z postanowieniami.
   * Funkcje te obejmują:
   *  - dodawanie nowego postanowienia przy dodawaniu pisma wychodzącego
   *  - edycję istniejącego postanowienia - znak i dotyczy
   *  - tworzenie zestawień postanowień w podziale na rok i/lub jrwa
   *
   *  Postanowienie stanowi całość dopiero w połączeniu z pismem wychodzącym.
   *  Samodzielnie nie może istnieć - brak sprawy, brak odbiorcy.
   *
   */

  class Postanowienie {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function dodajPostanowienie($id_wychodzace, $numer, $dotyczy, $id_jrwa) {
      /*
       * Dodaje postanowienie do bazy danych.
       *
       * Parametry:
       *  - id_wychodzace => id pisma wychodzącego, do którego postanowienie jest przypisane
       *  - numer => treść pola numer postanowienia
       *  - dotyczy => treść pola dotyczy postanowienia
       *  - id_jrwa => id numeru jrwa sprawy w ramach której dodawane jest postanowienie
       * Zwraca:
       *  - obiekt dodanego postanowienia
       */

      $sql = "INSERT INTO postanowienia (id_wychodzace, numer, dotyczy, id_jrwa)
              VALUES (:id_wychodzace, :numer, :dotyczy, :id_jrwa)";
      $this->db->query($sql);
      $this->db->bind(':id_wychodzace', $id_wychodzace);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':dotyczy', $dotyczy);
      $this->db->bind(':id_jrwa', $id_jrwa);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      if ($this->db->execute()) {
        $sql = "SELECT * FROM postanowienia
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

    public function pobierzLiczbePostanowienWRamachJrwa($jrwa, $rok) {
      /*
       * Pobiera liczbę postanowień w ramach podanego jrwa utworzonych w danym roku.
       *
       * Parametry:
       *  - jrwa => id numeru jrwa
       *  - rok => rok utworzenia postanowienia
       * Zwraca:
       *  - int => liczba postanowień spełniających kryteria
       */

      $sql = "SELECT COUNT(id) AS liczba FROM postanowienia
                   WHERE id_jrwa=:id_jrwa AND utworzone LIKE :rok";
      $this->db->query($sql);
      $this->db->bind(':id_jrwa', $jrwa);
      $this->db->bind(':rok', $rok . "%");
      $row = $this->db->single();
      return $row->liczba;
    }

    public function pobierzPostanowienia($rok, $jrwa) {
      /*
       * Pobiera wszystkie dane o postanowieniach wystawionych w danym roku.
       *
       * Parametr jrwa to numer jrwa a nie jego id - prościej w adresie i formularzu
       *
       * Parametry:
       *  - rok => rok wystawienia postanowienia
       *  - jrwa => numer jrwa (nie id)
       * Zwraca:
       *  - set zawierający obiekty postanowień; set jest posortowany rosnąco po znaku sprawy i dacie postanowienia
       */

      // użyj wildcard jeżeli nie wybrano jrwa
      if ($jrwa == '') {
        $jrwa="%";
      }

      $sql = "SELECT wychodzace.*,
                     postanowienia.id AS postanowienieId,
                     postanowienia.numer AS postanowienieNumer,
                     postanowienia.dotyczy AS postanowienieDotyczy,
                     postanowienia.utworzone AS postanowienieData,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy, postanowienia, jrwa
                     WHERE postanowienia.utworzone LIKE :rok
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id
                       AND wychodzace.id=postanowienia.id_wychodzace
                       AND postanowienia.id_jrwa=jrwa.id
                       AND jrwa.numer LIKE :jrwa
                     ORDER BY sprawy.znak, postanowienia.utworzone ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . "%");
      $this->db->bind(':jrwa', $jrwa);

      return $this->db->resultSet();
    }

    public function pobierzPostanowieniePoId($id) {
      /*
       * Pobiera wszystkie dane o wybranym postanowieniu.
       *
       * Parametry:
       *  - id => id szukanego postanowienia
       * Zwraca:
       *  - obiekt postanowienia
       */

      $sql = "SELECT * FROM postanowienia WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function edytujPostanowienie($id, $numer, $dotyczy) {
      /*
       * Zmienia dane wybranego postanowienia.
       * Ze względu na wygodę wstawia wszystkie nowe dane bez sprawdzania, które faktycznie się zmieniły.
       *
       * Parametry:
       *  - id => id postanowienia do zmiany
       *  - numer => nowa wartość pola numer postanowienia
       *  - dotyczy => nowa wartość pola dotyczy postanowienia
       * Zwraca:
       *  - brak
       */

      $sql = "UPDATE postanowienia SET numer=:numer, dotyczy=:dotyczy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':numer', $numer);
      $this->db->bind(':dotyczy', $dotyczy);

      $this->db->execute();
    }

  }


?>
