<?php
  /*
   * Model Admin obsługuje wszystkie funkcje związane z adminem, czyli:
   *  - dodanie admina, gdy nie istnieje
   *  - logowanie
   *  - zmianę hasła
   *  - ustawianie terminu ważności hasła (nie ma sensu tworzyć osobnego modelu dla jednej funkcji)
   *
   * W systemie istnieje tylko jedno konto admina, które
   * posiada orębną tabelę w bazie danych.
   */

  class Admin {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function czyIstniejeAdmin() {
      /*
       * Sprawdza czy dla systemu ustanowiony jest admin.
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - boolean => true jeżeli admin istnieje
       */

      $sql = "SELECT id FROM admin WHERE login='admin'";
      $this->db->query($sql);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return false;
      } else {
        return true;
      }
    }

    public function dodajAdmina($data) {
      /*
       * Tworzy konto admina w systemie.
       * Login admina to 'admin' - hasło wybrane przez użytkownika.
       *
       * Parametry:
       *  - data => dane z formularza - interesuje nas tylko zakododane hasło
       * Zwraca:
       *  - boolean
       */

      $login = 'admin';
      $haslo = $data['haslo1'];

      $sql = "INSERT INTO admin (login, haslo) VALUES (:login, :haslo)";
      $this->db->query($sql);
      $this->db->bind(':login', $login);
      $this->db->bind(':haslo', $haslo);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function zmienHaslo($haslo) {
      /*
       * Zmienia hasło admina.
       *
       * Parametry:
       *  - haslo => nowe hasło
       * Zwraca:
       *  - boolean => true jeżeli zmiana przebiegła pomyślnie
       */

      $sql = "UPDATE admin SET haslo=:haslo WHERE login='admin'";
      $this->db->query($sql);
      $this->db->bind(':haslo', $haslo);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function czyPoprawneHaslo($haslo) {
      /*
       * Sprawdza poprawność hasła admina.
       *
       * Parametry:
       *  - haslo => hasło do sprawdzenia
       * Zwraca:
       *  - boolean => true jeżeli hasło jest poprawne
       */

      $sql = "SELECT haslo FROM admin WHERE login='admin'";
      $this->db->query($sql);
      $row = $this->db->single();

      return password_verify($haslo, $row->haslo);
    }

    public function pobierzTerminWaznosciHasla() {
      /*
       * Zwraca termin ważności hasła z tabeli ustawienia
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - int => termin ważności hasła w dniach; 0 oznacza, że hasła nie mają terminu ważności
       */

      $sql = "SELECT waznosc_hasla FROM ustawienia WHERE id='1'";
      $this->db->query($sql);
      $row = $this->db->single();

      return $row->waznosc_hasla;
    }

    public function ustawTerminWaznosciHasla($termin) {
      /*
       * Ustawia termin ważności hasła w tabeli ustawienia
       *
       * Parametry:
       *  - termin => nowy termin w dniach
       * Zwraca:
       *  - brak
       */

      $sql = "UPDATE ustawienia SET waznosc_hasla=:termin WHERE id='1'";
      $this->db->query($sql);
      $this->db->bind(':termin', $termin);
      $this->db->execute();
    }



  }


