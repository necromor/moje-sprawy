<?php
  /*
   * Model Podmiot obsługuje wszystkie funkcje związane z podmiotami, takie jak:
   * - dodanie nowego
   * - edycja istniejącego
   * - tworzenie zbiorów danych o podmiotach
   * 
   * Podmiot to wspólny model dla Nadawcy i Odbiorcy pisma.
   * W obu przypadkach istnieje tylko jeden model - nazywany inaczej
   * w formularzach ze względów stylistycznych lub przez przeoczenie.
   *
   */

  class Podmiot {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function dodajPodmiot($data) {
      /*
       * Dodaje nowy podmiot do bazy danych.
       *
       * Parametry: 
       *  - data => tablica zawierająca dane nowego podmiotu  
       * Zwraca: 
       *  - boolean 
       */

      $sql = "INSERT INTO podmioty (nazwa, adres_1, adres_2) VALUES (:nazwa, :adres_1, :adres_2)";
      $this->db->query($sql);
      $this->db->bind(':nazwa', $data['nazwa_podmiotu']);
      $this->db->bind(':adres_1', $data['adres_podmiotu']);
      $this->db->bind(':adres_2', $data['poczta_podmiotu']);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function edytujPodmiot($data) {
      /*
       * Zmienia dane istniejącego podmiotu.
       * W danych wejściowych podane jest id podmiotu do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * Parametry: 
       *  - data => tablica zawierająca nowe dane istniejącego podmiotu  
       * Zwraca: 
       *  - boolean 
       */

      $sql = "UPDATE podmioty SET nazwa=:nazwa, adres_1=:adres_1, adres_2=:adres_2 WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':nazwa', $data['nazwa_podmiotu']);
      $this->db->bind(':adres_1', $data['adres_podmiotu']);
      $this->db->bind(':adres_2', $data['poczta_podmiotu']);
      $this->db->bind(':id', $data['id']);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

   public function pobierzPodmioty() {
      /*
       * Pobiera listę wszystkich podmiotów posortowaną rosnąco po id.
       *
       * Parametry: 
       *  - brak 
       * Zwraca: 
       *  - set zawierający wszystkie dane o podmiocie 
       */

     $sql = "SELECT * FROM podmioty ORDER BY id ASC";
     $this->db->query($sql);

     return $this->db->resultSet(); 
   }

   public function pobierzDanePodmiotu($id) {
      /*
       * Pobiera wszystkie dane o wybranym podmiocie na podstawie jego id.
       *
       * Parametry: 
       *  - id => id szukanego podmiotu 
       * Zwraca: 
       *  - wiersz z bazy zawierający wszystkie dane o podmiocie 
       */

     $sql = "SELECT * FROM podmioty WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);
     
     return $this->db->single();
   }

   public function pobierzDanePodmiotuPoNazwie($nazwa) {
      /*
       * Pobiera wszystkie dane o wybranym podmiocie na podstawie jego nazwy.
       * Ponieważ nazwa podmiotu nie jest unikalna z bazy danych pobierany
       * jest podmiot najnowszy.
       * Funkcja ma głównie zastosowanie jeżeli dodany został nowy podmiot.
       * W każdym innym przypadku pewniejsza jest pobierzDanePodmiotu.
       *
       * Parametry: 
       *  - nazwa => id szukanego podmiotu 
       * Zwraca: 
       *  - wiersz z bazy zawierający wszystkie dane o podmiocie 
       */

     $sql = "SELECT * FROM podmioty WHERE nazwa=:nazwa ORDER BY id DESC LIMIT 1";
     $this->db->query($sql);
     $this->db->bind(':nazwa', $nazwa);
     
     return $this->db->single();
   }

   public function czyIstniejePodmiot($id) {
      /*
       * Sprawdza czy podmiot z podanym id istnieje w bazie danych.
       *
       * Parametry: 
       *  - id => id szukanego podmiotu 
       * Zwraca: 
       *  - boolean
       */

     $sql = "SELECT * FROM podmioty WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);
     
     if ($this->db->single()) {
       return true;
     } else {
       return false;
     }
   }


  }
