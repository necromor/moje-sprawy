<?php
  /*
   *  Kontroler Decyzje odpowiedzialny jest za obsługę modelu Decyzja z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Decyzje extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->decyzjaModel = $this->model('Decyzja');
      $this->jrwaModel = $this->model('JrwaM');

      //$this->validator = new Validator();
    }

    public function zestawienie($rok, $jrwa='') {
      /*
       * Tworzy zestaw obiektów Wychodzace
       *
       * Obsługuje widok: decyzje/zestawienie/rok/jrwa
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        // sprawdź czy przesłany został numer jrwa
        if (isset($_POST['jrwa']) && trim($_POST['jrwa']) != '') {

          redirect('decyzje/zestawienie/' . $_POST['rok'] . '/' . $_POST['jrwa']);
        } else {
          redirect('decyzje/zestawienie/' . $_POST['rok']);
        }
      }

      $decyzje = $this->decyzjaModel->pobierzDecyzje($rok, $jrwa);
      $jrwaLista = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Zestawienie decyzji',
        'rok' => $rok,
        'jrwa' => $jrwa,
        'decyzje' => $decyzje,
        'jrwaLista' => $jrwaLista,
        'rok' => $rok
      ];

      $this->view('decyzje/zestawienie', $data);
    }

    public function ajax_numer_kolejny($jrwa) {
       /*
        * Pobiera kolejny numer decyzji w obecnym roku w ramach danego jrwa i drukuje je w postaci json.
        * Zastosowanie do zapytania ajax.
        *
        * Funkcja nie obsługuje widoku.
        *
        * Parametry:
        *  - jrwa => id numeru jrwa, dla którego szukamy decyzji w obecnym roku
        * Zwraca:
        *  - echo json postaci: { numer: }
        */

       // tylko zalogowany
       sprawdzCzyPosiadaDostep(4,0);
       $rok = Date("Y");

       $numer = $this->decyzjaModel->pobierzLiczbeDecyzjiWRamachJrwa($jrwa, $rok);
       $numer++;
       echo '{ "numer": "' . $numer . '"}';
    }



  }
