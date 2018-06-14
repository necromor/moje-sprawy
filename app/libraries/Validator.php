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
      //$this->pracownikModel = $this->model('Pracownik');
      $this->jrwaModel = $this->model('JrwaM');
    }

    public function sprawdzDlugosc($tekst, $dlugosc=0, $czySprawdzac=1) {
      /*
       * Sprawdza długość wprowadzonego tekstu na dwa sposoby:
       *  a) czy tekst jest
       *  b) czy długość tekstu jest przynajmniej równa długosc.
       *
       *  Parametry:
       *   - tekst => wartość pola
       *   - dlugosc => minimalna liczba znaków jaką musi mieć tekst
       *   - czySprawdzac => dodatkowy warunek o wartości 0 lub 1, gdzie 1 oznacza pominięcie sprawdzania
       *  Zwraca:
       *   - sting zawierający komunikat błędu jeżeli taki wystąpł
       */

      if ($czySprawdzac == '0') {
        return '';
      }

      if ($tekst == '') {
        return "Pole nie może być puste.";
      }

      if (strlen($tekst) < $dlugosc ) {
        return "Tekst w polu musi mieć przynajmniej $dlugosc znaków.";
      }

    }

    public function sprawdzJrwa($tekst) {
      /*
       * Sprawdza poprawność wprowadzonego numeru jrwa do formularza.
       * Zasady:
       *  - pole nie może być puste
       *  - numer musi istnieć w bazie danych
       *
       *  Parametry:
       *   - tekst => wprowadzony numer
       *  Zwraca:
       *   - string zawierający komunikat błędu jeżeli taki wystąpł
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
      *  Parametry:
      *   - tekst => wprowadzony numer
      *   - id => id edytowanego numeru
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
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



  }
