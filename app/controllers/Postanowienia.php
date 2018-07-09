<?php
  /*
   *  Kontroler Postanowienia odpowiedzialny jest za obsługę modelu Postanowienia z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Postanowienia extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->postanowienieModel = $this->model('Postanowienie');
      $this->wychodzacaModel = $this->model('Wychodzaca');
      $this->jrwaModel = $this->model('JrwaM');

      $this->validator = new Validator();
    }

    public function index() {
      /*
       * Służy ona do obsługi wszystkich adresów, które nie mają odzwierciedleń w funkcjach.
       *
       * Z uwagi na konstrukcję TraversyMVC żądania nie mające funkcji
       * będa wyświetlać błąd jeżeli nie będzie index.
       *
       * Przekierowuje na 'pages', które dzieli w zależności od poziomu dostępu.
       */

      redirect('pages');
    }

    public function zestawienie($rok, $jrwa='') {
      /*
       * Tworzy zestaw obiektów Postanowienia
       *
       * Obsługuje widok: postanowienia/zestawienie/rok/jrwa
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        // sprawdź czy przesłany został numer jrwa
        if (isset($_POST['jrwa']) && trim($_POST['jrwa']) != '') {

          redirect('postanowienia/zestawienie/' . $_POST['rok'] . '/' . $_POST['jrwa']);
        } else {
          redirect('postanowienia/zestawienie/' . $_POST['rok']);
        }
      }

      $postanowienia = $this->postanowienieModel->pobierzPostanowienia($rok, $jrwa);
      $jrwaLista = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Zestawienie postanowień',
        'rok' => $rok,
        'jrwa' => $jrwa,
        'postanowienia' => $postanowienia,
        'jrwaLista' => $jrwaLista,
        'rok' => $rok
      ];

      $this->view('postanowienia/zestawienie', $data);
    }

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji postanowienia.
       *
       * Zmianie podlega jedynie numer postanowienia i dotyczy.
       *
       * Pod pomyślnej edycji powrót następuje do zestawienia lub szczegółów sprawy w zależności skąd było wywołanie.
       *
       * Obsługuje widok: postanowienia/edytuj
       *
       * Parametry:
       *  - id => id postanowienia do zmiany
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

      $postanowienie = $this->postanowienieModel->pobierzPostanowieniePoId($id);
      $pismo = $this->wychodzacaModel->pobierzWychodzacaPoId($postanowienie->id_wychodzace);

      $data = [
        'title' => 'Zmień dane postanowienia',
        'id' => $id,
        'zrodlo' => $zrodlo,
        'sprawaId' => $pismo->sprawaId,
        'rok' => substr($postanowienie->utworzone, 0 ,4), //potrzebne do powrotu jak źródło w zestawieniu
        'numer' => $postanowienie->numer,
        'dotyczy' => $postanowienie->dotyczy,
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

          $id_decyzji = $this->postanowienieModel->edytujPostanowienie($id, $data['numer'], $data['dotyczy']);

          $wiadomosc = "Postanowienie zostało zmienione pomyślnie.";
          if ($zrodlo == 'zestawienie') {
            flash('postanowienia_edytuj', $wiadomosc);
            redirect('postanowienia/zestawienie/'. substr($pismo->utworzone, 0, 4));
          } else {
            flash('sprawy_szczegoly', $wiadomosc);
            redirect('sprawy/szczegoly/'. $pismo->sprawaId);
          }
        }
      }

      $this->view('postanowienia/edytuj', $data);
    }

    public function ajax_numer_kolejny($jrwa) {
       /*
        * Pobiera kolejny numer postanowienia w obecnym roku w ramach danego jrwa i drukuje je w postaci json.
        * Zastosowanie do zapytania ajax.
        *
        * Funkcja nie obsługuje widoku.
        *
        * Parametry:
        *  - jrwa => id numeru jrwa, dla którego szukamy postanowienia w obecnym roku
        * Zwraca:
        *  - echo json postaci: { numer: }
        */

       // tylko zalogowany
       sprawdzCzyPosiadaDostep(4,0);
       $rok = Date("Y");

       $numer = $this->postanowienieModel->pobierzLiczbePostanowienWRamachJrwa($jrwa, $rok);
       $numer++;
       echo '{ "numer": "' . $numer . '"}';
    }



  }
