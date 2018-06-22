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
      $this->wychodzacaModel = $this->model('Wychodzaca');
      $this->jrwaModel = $this->model('JrwaM');

      $this->validator = new Validator();
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

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji decyzji.
       *
       * Zmianie podlega jedynie numer decyzji i dotyczy.
       *
       * Pod pomyślnej edycji powrót następuje do zestawienia lub szczegółów sprawy w zależności skąd było wywołanie.
       *
       * Obsługuje widok: decyzje/edytuj
       *
       * Parametry:
       *  - id => id decyzji do zmiany
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // określenie skąd użytkownik przeszedł do edycji pisma
      if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $zrodlo = 'szczegoly';
        if (strpos($_SERVER['HTTP_REFERER'], 'zestawienie')) {
          $zrodlo = 'zestawienie';
        }
      } else {
        $zrodlo = $_POST['zrodlo'];
      }

      $decyzja = $this->decyzjaModel->pobierzDecyzjePoId($id);
      $pismo = $this->wychodzacaModel->pobierzWychodzacaPoId($decyzja->id_wychodzace);

      $data = [
        'title' => 'Zmień dane decyzji',
        'id' => $id,
        'zrodlo' => $zrodlo,
        'sprawaId' => $pismo->sprawaId,
        'rok' => substr($decyzja->utworzone, 0 ,4), //potrzebne do powrotu jak źródło w zestawieniu
        'numer' => $decyzja->numer,
        'dotyczy' => $decyzja->dotyczy,
        'numer_err' => '',
        'dotyczy_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['numer'] = trim($_POST['numer']);
        $data['dotyczy'] = trim($_POST['dotyczy']);

        $data['numer_err'] = $this->validator->sprawdzDlugosc($data['numer'], 1);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);

        if (empty($data['numer_err']) &&
            empty($data['dotyczy_err'])) {

          $id_decyzji = $this->decyzjaModel->edytujDecyzja($id, $data['numer'], $data['dotyczy']);

          $wiadomosc = "Decyzja została zmieniona pomyślnie.";
          if ($zrodlo == 'zestawienie') {
            flash('decyzje_edytuj', $wiadomosc);
            redirect('decyzje/zestawienie/'. substr($pismo->utworzone, 0, 4));
          } else {
            flash('sprawy_szczegoly', $wiadomosc);
            redirect('sprawy/szczegoly/'. $pismo->sprawaId);
          }
        }
      }

      $this->view('decyzje/edytuj', $data);
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
