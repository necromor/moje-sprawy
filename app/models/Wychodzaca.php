<?php
  /*
   * Model Wychodzące obsługuje wszystkie funkcje związane
   * z korespondencją wychodząca.
   * Funkcje te obejmują:
   *  - dodawanie nowej korespondencji w ramach sprawy
   *  - zwykłe pismo lub decyzja lub postanowienie (osobne modele)
   *  - edycję istniejącej korespondencji
   *  - tworzenie zestawień korespondencji ?
   *
   */

  class Wychodzaca {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzWychodzace($rok) {
      /*
       * Pobiera wszystkie dane o pismach wychodzących dodanych w danym roku.
       *
       * Parametry:
       *  - rok => rok dodania pisma do bazy danych
       * Zwraca:
       *  - set zawierający dane pism
       */

      $sql = "SELECT wychodzace.*,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy
                     WHERE wychodzace.utworzone LIKE :rok
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id
                     ORDER BY sprawy.znak ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . '%');

      return $this->db->resultSet();
    }



    public function pobierzWychodzacaPoId($id) {
      /*
       * Pobiera wszystkie dane o korespondencji na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanej korespondencji
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane korespondencji
       */

      $sql = "SELECT wychodzace.*,
                     decyzje.id AS decyzjaId,
                     decyzje.numer AS decyzjaNumer,
                     decyzje.dotyczy AS decyzjaDotyczy
                FROM
                  (SELECT
                     wychodzace.*,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy
                     WHERE wychodzace.id=:id
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id)wychodzace
                LEFT OUTER JOIN decyzje ON wychodzace.id=decyzje.id_wychodzace";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function dodajWychodzaca($data) {
      /*
       * Dodaje pismo wychodzące do bazy danych.
       * Data pisma to data utworzenia rekordu w bazie danych.
       *
       * Parametry:
       *  - data => tablica zawierająca dane nowego pisma
       * Zwraca:
       *  - id dodanego pisma
       */

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "INSERT INTO wychodzace (id_sprawa, id_podmiot, dotyczy)
              VALUES (:id_sprawa, :id_podmiot, :dotyczy)";
      $this->db->query($sql);
      $this->db->bind(':id_sprawa', $data['id']);
      $this->db->bind(':id_podmiot', $id_podmiot);
      $this->db->bind(':dotyczy', $data['dotyczy']);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      if ($this->db->execute()) {
        $sql = "SELECT id FROM wychodzace
                  WHERE id_sprawa=:id_sprawa
                    AND id_podmiot=:id_podmiot
                    AND dotyczy=:dotyczy
                  ORDER BY utworzone DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id_sprawa', $data['id']);
        $this->db->bind(':id_podmiot', $id_podmiot);
        $this->db->bind(':dotyczy', $data['dotyczy']);
        $row = $this->db->single();
        return $row->id;
      }
    }

    public function edytujWychodzaca($data) {
      /*
       * Zmienia dane istniejącegeo pisma.
       * W danych wejściowych podane jest id pisma do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * WAŻNE:
       * Data pisma czyli utworzone nie podlega zmianie!
       * Czy sprawa ulega zmianie to DO USTALENIA.
       *
       * Parametry:
       *  - data => tablica zawierająca dane istniejącego pracownika
       * Zwraca:
       *  - boolean
       */

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "UPDATE wychodzace SET dotyczy=:dotyczy, id_podmiot=:id_podmiot WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':id_podmiot', $id_podmiot);

      // zmień dane w bazie
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }



  }
