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

    public function zestawienie($rok) {
      /*
       * Tworzy zestaw obiektów Wychodzace
       *
       * Obsługuje widok: wychodzace/zestawienie/rok
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        redirect('wychodzace/zestawienie/' . $_POST['rok']);
      }

      $pisma = $this->wychodzacaModel->pobierzWychodzace($rok);

      $data = [
        'title' => 'Zestawienie pism wychodzących',
        'pisma' => $pisma,
        'rok' => $rok
      ];

      $this->view('wychodzace/zestawienie', $data);
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
