<?php
  /*
   * Klasa Validator odpowiada za wszystkie funkcje sprawdzające
   * pola formularza.
   * Posiada funkcje uniwersalne jak i specificzne dla konkretnego modelu.
   *
   */

  class Validator extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      //$this->podmiotModel = $this->model('Podmiot');
      //$this->przychodzacaModel = $this->model('Przychodzaca');
      $this->pracownikModel = $this->model('Pracownik');
      $this->jrwaModel = $this->model('JrwaM');
    }

    public function sprawdzDlugosc($tekst, $dlugosc=0, $czySprawdzac=1) {
      /*
       * Sprawdza długość wprowadzonego tekstu na dwa sposoby:
       *  a) czy tekst jest
       *  b) czy długość tekstu jest przynajmniej równa długosc.
       *
       * Parametry:
       *  - tekst => wartość pola
       *  - dlugosc => minimalna liczba znaków jaką musi mieć tekst
       *  - czySprawdzac => dodatkowy warunek o wartości 0 lub 1, gdzie 1 oznacza pominięcie sprawdzania
       * Zwraca:
       *  - string zawierający komunikat błędu jeżeli taki wystąpił
       */

      if ($czySprawdzac == '0') {
        return '';
      }

      if ($tekst == '') {
        return "Pole nie może być puste.";
      }

      if (strlen($tekst) < $dlugosc ) {
        return "Minimalna wymagana liczba znaków w polu to: $dlugosc.";
      }

    }

    public function sprawdzJrwa($tekst) {
      /*
       * Sprawdza poprawność wprowadzonego numeru jrwa do formularza.
       * Zasady:
       *  - pole nie może być puste
       *  - numer musi istnieć w bazie danych
       *
       * Parametry:
       *  - tekst => wprowadzony numer
       * Zwraca:
       *  - string zawierający komunikat błędu jeżeli taki wystąpił
       */

      if ($tekst == '') {
        return "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
      }

      if (!$this->jrwaModel->czyIstniejeJrwa($tekst, 0)) {
        return "Podany numer JRWA nie istnieje.";
      }

      return '';
    }

   public function sprawdzNumerJrwa($tekst, $id=0) {
     /*
      * Sprawdza poprawność wprowadzonego numeru jrwa do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - numer musi mieć od 1 do 4 cyfr
      *  - numer nie może istnieć w bazie danych
      * Przy edycji numeru ostatni warunek musi uwzględniać fakt, że użytkownik może nie chcieć zmienić
      * istniejącego numeru.
      *
      * Funkcja inna niż sprawdzJrwa, gdyż zapobiega duplikatom, a sprawdzJrwa używa istniejących numerów.
      *
      * Parametry:
      *  - tekst => wprowadzony numer
      *  - id => id edytowanego numeru
      * Zwraca:
      *  - string zawierający komunikat błędu jeżeli taki wystąpił
      */

     if ($tekst == '') {
       return "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
     }

     if (!preg_match('/^[0-9]{1,4}$/', $tekst)) {
       return "Format numeru jrwa to od 1 do 4 cyfr.";
     }

     if ($this->jrwaModel->czyIstniejeJrwa($tekst, $id)) {
       return "W bazie danych istnieje już JRWA o numerze $tekst.";
     }

     return '';
   }

   public function sprawdzLogin($login, $id=0) {
     /*
      * Sprawdza poprawność wprowadzonego loginu do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - login musi mieć przynajmniej 2 znaki
      *  - login musi być unikatowy
      *
      * Parametry:
      *  - login => wprowadzony login
      *  - id => id użytkownika jeżeli sprawdzanie przy edycji
      * Zwraca:
      *  - string zawierający komunikat błędu jeżeli taki wystąpił
      */

     if ($login == '') {
       return "Pracownik musi mieć login.";
     }

     if (strlen($login) < 2) {
       return "Login musi mieć przynajmniej 2 znaki.";
     }

     if ($this->pracownikModel->czyIstniejeLogin($login, $id)) {
       return "Podany login jest już zajęty.";
     }

     return '';
   }

   public function sprawdzLoginLogowanie($login) {
     /*
      * Sprawdza poprawność wprowadzonego loginu do formularza logowania.
      * Login musi należeć do aktywnego pracownika.
      *
      * Parametry:
      *  - login => wprowadzony login do formularza
      * Zwraca:
      *  - string zawierający komunikat błędu jeżeli taki wystąpił
      */

     if ($login == '') {
       return "Proszę podać login.";
     }

     $id = $this->pracownikModel->pobierzIdPracownikaPoLoginie($login);

     if (!$this->pracownikModel->czyAktywny($id)) {
       return "Podany login należy do nieaktywnego pracownika.";
     }

     if ($id == -1) {
       return "Podany login jest nieprawidłowy.";
     }

     return '';
   }

   public function sprawdzHaslo($haslo, $id) {
     /*
      * Sprawdza poprawność wprowadzonego hasła do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - hasło musi być zgodne z tym w bazie danych
      *
      * Parametry:
      *  - haslo => wprowadzone hasło
      *  - id => id użytkownika, którego login wprowadzony został do formularza
      * Zwraca:
      *  - string zawierający komunikat błędu jeżeli taki wystąpił
      */

     if ($haslo == '') {
       return "Musisz podać hasło.";
     }

     if (!$this->pracownikModel->sprawdzHaslo($haslo, $id)) {
       return "Podano nieporawne hasło.";
     }

     return '';
   }

   public function sprawdzNoweHaslo($haslo, $stare, $dlugosc=6) {
     /*
      * Sprawdza poprawność wprowadzonego nowego hasła do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - hasło musi mieć przynajmniej X znaków
      *  - nowe hasło nie może być takie samo jak stare
      *
      *  Parametry:
      *   - haslo => wprowadzone nowe hasło
      *   - stare => zmieniane hasło
      *   - dlugosc => minimalna długość nowego hasła
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpił
      */

     if ($haslo == '') {
       return "Musisz podać nowe hasło.";
     }

     if (strlen($haslo) < $dlugosc) {
       return "Hasło musi mieć przynajmniej $dlugosc znaków.";
     }

     if ($haslo == $stare) {
       return "Nowe hasło musi być inne niż obecne.";
     }

     return '';
   }

  }
