<?php
  /*
   * Model Inny obsługuje wszystkie funkcje związane
   * z innymi dokumentami.
   * Funkcje te obejmują:
   *  - dodawanie nowego dokumentu w ramach sprawy
   *  - edycję istniejącego dokumentu
   *
   */

  class Inny {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzInnyDokumentPoId($id) {
      /*
       * Pobiera wszystkie dane o korespondencji na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanej korespondencji
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane korespondencji
       */

      $sql = "SELECT * FROM inne WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function dodajInnyDokument($data) {
      /*
       * Dodaje inny dokument do bazy danych.
       *
       * Parametry:
       *  - data => tablica zawierająca dane nowego dokumentu
       * Zwraca:
       *  - id dodanego dokumentu
       */

      $sql = "INSERT INTO inne (id_sprawa, rodzaj, dotyczy)
              VALUES (:id_sprawa, :rodzaj, :dotyczy)";
      $this->db->query($sql);
      $this->db->bind(':id_sprawa', $data['id']);
      $this->db->bind(':rodzaj', $data['rodzaj']);
      $this->db->bind(':dotyczy', $data['dotyczy']);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      if ($this->db->execute()) {
        $sql = "SELECT id FROM inne
                  WHERE id_sprawa=:id_sprawa
                    AND rodzaj=:rodzaj
                    AND dotyczy=:dotyczy
                  ORDER BY utworzone DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id_sprawa', $data['id']);
        $this->db->bind(':rodzaj', $data['rodzaj']);
        $this->db->bind(':dotyczy', $data['dotyczy']);
        $row = $this->db->single();
        return $row->id;
      }
    }

    public function edytujInnyDokument($data) {
      /*
       * Zmienia dane istniejącegeo dokumentu.
       * W danych wejściowych podane jest id pisma do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * Parametry:
       *  - data => tablica zawierająca dane istniejącego dokumentu
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE inne SET dotyczy=:dotyczy, rodzaj=:rodzaj WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':rodzaj', $data['rodzaj']);

      // zmień dane w bazie
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }



  }
