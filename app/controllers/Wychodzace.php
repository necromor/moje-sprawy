<?php
  /*
   *  Kontroler Wychodzace odpowiedzialny jest za obsługę modelu Wychodzaca z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Wychodzace extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->wychodzacaModel = $this->model('Wychodzaca');

      $this->validator = new Validator();
    }


   public function ajax_wychodzace($id) {
      /*
       * Pobiera dane pisma wychodzącego i drukuje je w postaci json.
       * Zastosowanie do zapytania ajax.
       * Jeżeli pismo nie istnieje w miejsu id wstawiona zostaje wartość -1
       *
       * Funkcja nie obsługuje widoku.
       *
       * Parametry:
       *  - id => id pobieranego pisma
       * Zwraca:
       *  - echo json postaci: { id:, znak:, itd... }
       */

       // tylko zalogowany
       sprawdzCzyPosiadaDostep(4,0);

       $pismo = $this->wychodzacaModel->pobierzWychodzacaPoId($id);
       if ($pismo) {
         echo json_encode($pismo);
       } else {
         echo '{"id":"-1"}';
       }
   }



  }
