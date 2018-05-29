<?php
  /*
   * Model Sprawa obsługuje wszystkie funkcje związane ze sprawami, takie jak:
   *  - dodanie nowej sprawy
   *  - przypisanie korespondencji do sprawy
   *  - usunięcie przypisanego pisma do sprawy
   *  - edycja tematu sprawy
   *
   * Dodatkowo model obsługuje funkcje związane z pobieraniem danych sprawy
   * na różne sposoby.
   *
   */

  class Sprawa {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }


   public function pobierzSprawePoId($id) {
      /*
       * Pobiera wszystkie dane o wybranej sprawie na podstawie jej id.
       *
       * Parametry:
       *  - id => id szukanej sprawy
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane o sprawie
       */

     $sql = "SELECT * FROM sprawy WHERE id=:id";
     $this->db->query($sql);
     $this->db->bind(':id', $id);

     return $this->db->single();
   }




  }
