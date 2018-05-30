<?php
  /*
   * Model Metryka obsługuje w zasadzie dwie funkcje:
   *  - utworzenie nowego wpisu w metryce dla sprawy
   *  - tworzenie zbioru wpisów dla sprawy
   *
   * Model Metryka nie funkcjonuje samodzielnie. Pełni służebną rolę
   * w stosunku do modelu Sprawa.
   *
   */

  class Metryka {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzMetrykeSprawy($id) {
      /*
       * Pobiera wszystkie wpisy metryki dla wybranej sprawy posortowane
       * według daty dodania od najwcześniejszego.
       *
       * Parametry:
       *  - id => id sprawy dla której szukane są wpisy
       * Zwraca:
       *  - set zawierający wiersze z bazy danych z wpisami metryki
       */

      $sql = "SELECT * FROM metryka WHERE id_sprawa=:id ORDER BY utworzone ASC";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->resultSet();
    }

    public function dodajMetryke($id_sprawa, $rodzaj, $pracownik, $rodzaj_dokumentu=0, $id_dokumentu=0) {
      /*
       * Dodaje nowy wpis metryki do bazy danych.
       * Opis czynności wybierany jest na podstawie zmiennej rodzaj:
       *  0 - rejestracja sprawy
       *  1 - dodanie przychodzącego
       *  2 - dodanie wychodzącego
       *  3 - dodanie innego dokumentu
       *  9 - edycja tematu sprawy
       *  98 - wznowienie sprawy
       *  99 - zakończenie sprawy
       *
       * Parametry:
       *  - id_sprawa => id sprawy dla której wpis metryki jest tworzony
       *  - rodzaj => określa opis podjętej czynności
       *  - pracownik => id pracownika, który dokonuje czynności
       *  - rodzaj_dokumentu => określa rodzaj dokumentu jakiego dotyczy czynność; 0 oznacza brak
       *  - id_dokumentu => określa id dokumentu jakiego dotyczy czynnośc; 0 oznacza brak
       * Zwraca:
       *  brak
       */

      $czynnosc = '';
      switch ($rodzaj) {
      case 0:
        $czynnosc = 'Rejestracja sprawy';
        break;
      case 1:
        $czynnosc = 'Przypisanie pisma przychodzącego do sprawy';
        break;
      case 2:
        $czynnosc = 'Dodanie pisma wychodzącego do sprawy';
        break;
      case 3:
        $czynnosc = 'Dodanie innego dokumentu do sprawy';
        break;
      case 9:
        $czynnosc = 'Zmiana tematu sprawy';
        break;
      case 98:
        $czynnosc = 'Wznowienie sprawy';
        break;
      case 99:
        $czynnosc = 'Zakończenie sprawy';
        break;
      default:
        $czynnosc = 'Niezidentyfikowana czynność nr '. $rodzaj;
        break;
      }
      $sql = "INSERT INTO metryka (id_sprawa, id_pracownik, czynnosc, rodzaj_dokumentu, id_dokument) VALUES (:id_sprawa, :id_pracownik, :czynnosc, :rodzaj_dokumentu, :id_dokumentu)";
      $this->db->query($sql);
      $this->db->bind(':id_sprawa', $id_sprawa);
      $this->db->bind(':id_pracownik', $pracownik);
      $this->db->bind(':czynnosc', $czynnosc);
      $this->db->bind(':rodzaj_dokumentu', $rodzaj_dokumentu);
      $this->db->bind(':id_dokumentu', $id_dokumentu);

      $this->db->execute();
    }





  }
