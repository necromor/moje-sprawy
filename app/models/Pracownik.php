<?php
  /*
   * Model Pracownik obsługuje wszystkie funkcje związane z pracownikami,
   * zarówno od strony admina jak i samego pracownika.
   * Admin ma możliwość:
   * - dodania nowego pracownika
   * - edycji istniejącego
   * - resetu hasła
   * - zmiany statusu na nieaktywny - USUWANIE nie jest możliwe ze względu
   * na istniejące sprawy i przypisane pisma do pracownika
   * Pracownik ma możliwości:
   * - zmiany hasła
   * - ustawienia znaku sprawy
   *
   * Dodatkowo model obejmuje funkcje związane z logowaniem pracownika
   * i pobieraniem jego danych na różne sposoby
   *
   */

  class Pracownik {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzPracownikaPoId($id) {
      /*
       * Pobiera wszystkie dane o pracowniku na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanego pracownika
       * Zwraca: 
       *  - wiersz z bazy zawierający wszystkie dane pracownika
       */

      $sql = "SELECT * FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function pobierzIdPracownikaPoLoginie($login) {
      /*
       * Pobiera id pracownika na podstawie podanego loginu.
       * Zwraca -1 jeżeli podany login nie istnieje w bazie danych.
       *
       * Parametry: 
       *  - login => login szukanego pracownika
       * Zwraca: 
       *  - int => id pracownika lub -1 gdy nie istnieje
       */

      $sql = "SELECT id FROM pracownicy WHERE login=:login";
      $this->db->query($sql);
      $this->db->bind(':login', $login);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return -1;
      } else {
        return $row->id;
      }
    }

    public function pobierzImieNazwisko($id) {
      /*
       * Pobiera imię i nazwisko pracownika na podstawie podanego id.
       *
       * Parametry: 
       *  - id => id szukanego pracownika
       * Zwraca: 
       *  - string w postaci "IMIĘ NAZWISKO" 
       */

      $sql = "SELECT imie, nazwisko FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row->imie . ' ' . $row->nazwisko;
    }

    public function pobierzPoziomDostepu($id) {
      /*
       * Pobiera poziom dostępu pracownika na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanego pracownika
       * Zwraca:
       *  - int => poziom dostępu pracownika
       */

      $sql = "SELECT poziom FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row->poziom;
    }

    public function pobierzPrzedrostek($id) {
      /*
       * Pobiera przedrostek znaku sprawy dla pracownika na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanego pracownika
       * Zwraca:
       *  - string => przedrostek znaku sprawy
       */

      $sql = "SELECT przedrostek FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row->przedrostek;
    }

    public function pobierzPrzyrostek($id) {
      /*
       * Pobiera przyrostek znaku sprawy dla pracownika na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanego pracownika
       * Zwraca:
       *  - string => przyrostek znaku sprawy
       */

      $sql = "SELECT przyrostek FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row->przyrostek;
    }


    public function pobierzPracownikow() {
      /*
       * Pobiera listę danych aktywnych pracowników posortowaną rosnąco po nazwisku.
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - set zawierający id, imię, nazwisko aktywnych pracowników
       */

     $sql = "SELECT id, imie, nazwisko FROM pracownicy WHERE aktywny=1 ORDER BY nazwisko ASC";
     $this->db->query($sql);

     return $this->db->resultSet();
    }

    public function pobierzWszystkichPracownikow() {
      /*
       * Pobiera listę wszystkich pracowników posortowaną rosnąco po id.
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - set zawierający wszystkie dane o pracowniku
       */

     $sql = "SELECT * FROM pracownicy ORDER BY id ASC";
     $this->db->query($sql);

     return $this->db->resultSet(); 
    }

    public function czyIstniejeLogin($login, $id) {
      /*
       * Sprawdza czy w bazie danych istnieje już podany login.
       * W celu uniknięcia błędu podczas edycji ze sprawdzania wyłączony
       * jest login aktualnie edytowanego pracownika.
       * Przy dodawaniu nowego pracownika wartość ta ustawiona jest na 0.
       *
       * Parametry:
       *  - login => login do sprawdzenia
       *  - id => id sprawdzanego pracownika
       * Zwraca:
       *  - boolean
       */

      $sql = "SELECT id FROM pracownicy WHERE login=:login AND id!=:id";
      $this->db->query($sql);
      $this->db->bind(':login', $login);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      if ($this->db->rowCount() == 0) {
        return false;
      } else {
        return true;
      }
    }

    public function sprawdzHaslo($haslo, $id) {
      /*
       * Sprawdza czy podane hasło jest zgodne z tym w bazie danych dla pracownika z id.
       * Hasła w bazie są kodowane więc użyta do porównania użyta jest funkcja password_verify()
       *
       * Parametry:
       *  - hasło => hasło do sprawdzenia
       *  - id => id pracownika dla którego sprawdzamy hasło
       * Zwraca:
       *  - boolean
       */

      $sql = "SELECT haslo FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $row = $this->db->single();

      return password_verify($haslo, $row->haslo);
    }

    public function czyAktywny($id) {
      /*
       * Sprawdza czy pracownik z danym id ma status aktywny.
       *
       * Parametry:
       *  - id => id pracownika dla którego sprawdzamy status
       * Zwraca:
       *  - boolean
       */

      $sql = "SELECT aktywny FROM pracownicy WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      $row = $this->db->single();
      if ($row->aktywny == 0) {
        return false;
      } else {
        return true;
      }
    }

    public function czyPosiadaWzorZnaku($id) {
      /*
       * Sprawdza czy pracownik z danym id posiada wzór znaku.
       *
       * Parametry:
       *  - id => id pracownika dla którego sprawdzamy wzór znaku
       * Zwraca:
       *  - boolean
       */

      return ($this->pobierzPrzedrostek($id) != '');
    }

    public function dodajPracownika($data) {
      /*
       * Dodaje nowego pracownika do bazy danych.
       * Nowy pracownik jest zawsze ustawiony jako aktywny.
       * Jego hasło (ustawione w kontrolerze) jest zakodowanym loginem.
       * Data zminy hasła ustawiana jest na datę dodania pracownika.
       *
       * Parametry:
       *  - data => tablica zawierająca dane nowego pracownik
       * Zwraca:
       *  - boolean
       */

      // wartości stałe
      $aktywny = 1; // nowozarejestrowany jest aktywny
      $zmiana_hasla = Date("Y-m-d H:i:s");

      $sql = "INSERT INTO pracownicy (imie, nazwisko, login, haslo, zmiana_hasla, poziom, aktywny) VALUES (:imie, :nazwisko, :login, :haslo, :zmiana_hasla, :poziom, :aktywny)";
      $this->db->query($sql);
      $this->db->bind(':imie', $data['imie']);
      $this->db->bind(':nazwisko', $data['nazwisko']);
      $this->db->bind(':login', $data['login']);
      $this->db->bind(':haslo', $data['haslo']);
      $this->db->bind(':zmiana_hasla', $zmiana_hasla);
      $this->db->bind(':poziom', $data['poziom']);
      $this->db->bind(':aktywny', $aktywny);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function edytujPracownika($data) {
      /*
       * Zmienia dane istniejącego pracownika.
       * W danych wejściowych podane jest id pracownika do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * Parametry:
       *  - data => tablica zawierająca dane istniejącego pracownika
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE pracownicy SET imie=:imie, nazwisko=:nazwisko, login=:login, poziom=:poziom WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':imie', $data['imie']);
      $this->db->bind(':nazwisko', $data['nazwisko']);
      $this->db->bind(':login', $data['login']);
      $this->db->bind(':poziom', $data['poziom']);
      $this->db->bind(':id', $data['id']);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function zmienStatus($id, $status) {
      /*
       * Zmienia status pracownika o podanym id.
       *
       * Parametry:
       *  - id => id pracownika
       *  - status => nowy status
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE pracownicy SET aktywny=:status WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':status', $status);
      $this->db->bind(':id', $id);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function zmienHaslo($id, $haslo) {
      /*
       * Zmienia hasło pracownika o podanym id.
       * W momencie zmiany hasła ustawiana jest data zmiany.
       *
       * Parametry:
       *  - id => id pracownika
       *  - status => nowe hasło
       * Zwraca:
       *  - boolean
       */
      $zmiana_hasla = Date("Y-m-d H:i:s");

      $sql = "UPDATE pracownicy SET haslo=:haslo, zmiana_hasla=:zmiana_hasla WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':haslo', $haslo);
      $this->db->bind(':id', $id);
      $this->db->bind(':zmiana_hasla', $zmiana_hasla);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function ustawWzorZnakuSprawy($id, $przedrostek, $przyrostek) {
      /*
       * Ustawia wzór znaku sprawy dla pracownika o podanym id.
       *
       * Parametry:
       *  - id => id pracownika
       *  - przedrostek => przedrostek wzoruz znaku sprawy
       *  - przyrostek => przyrostek wzoruz znaku sprawy
       * Zwraca:
       *  - boolean
       */

      $sql = "UPDATE pracownicy SET przedrostek=:przedrostek, przyrostek=:przyrostek WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':przedrostek', $przedrostek);
      $this->db->bind(':przyrostek', $przyrostek);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

  }
