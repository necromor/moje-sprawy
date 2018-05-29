<?php
  /*
   *  Kontroler Sprawy odpowiedzialny jest za obsługę modelu Sprawa z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Sprawy extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami 
       */

      $this->sprawaModel = $this->model('Sprawa');
    }

    public function szczegoly($id) {
      /*
       * Serce całego modułu spraw.
       *
       * Wyświetla szczegóły sprawy czyli:
       *  - metrykę
       *  - przypisane do niej dokumenty
       *  - guziki umożliwiające przypisywanie pism
       *  - guziki umożliwiające edycję tematu
       *
       * Parametry:
       *  - id => id wyświetlanej sprawy
       *
       * Obsługuje widok: sprawy/szczegoly/id
       *
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $sprawa = $this->sprawaModel->pobierzSprawePoId($id);


      $data = [
        'title' => 'Szczegóły sprawy ' . $sprawa->znak,
        'id' => $id,
        'sprawa' => $sprawa
      ];

      $this->view('sprawy/szczegoly', $data);
    }





  }
