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





  }
