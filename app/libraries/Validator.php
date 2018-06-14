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

      $this->podmiotModel = $this->model('Podmiot');
      $this->przychodzacaModel = $this->model('Przychodzaca');
      $this->pracownikModel = $this->model('Pracownik');
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






  }
