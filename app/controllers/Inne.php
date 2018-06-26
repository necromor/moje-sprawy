<?php
  /*
   *  Kontroler Inne odpowiedzialny jest za obsługę modelu Inny z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Inne extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->innyModel = $this->model('Inny');
      $this->metrykaModel = $this->model('Metryka');

      $this->validator = new Validator();
    }

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji innego dokumentu.
       *
       * W ramach edycji pisma użytkownik może zmienić tylko rodzaj dokumentu oraz treść dotyczy.
       *
       * Pod pomyślnej edycji powrót następuje do zestawienia.
       *
       * Obsługuje widok: inne/edytuj
       *
       * Parametry:
       *  - id => id dokumentu do zmiany
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $dokument = $this->innyModel->pobierzInnyDokumentPoId($id);

      $data = [
        'title' => 'Zmień dane innego dokumentu',
        'id' => $id,
        'sprawaId' => $dokument->id_sprawa,
        'rodzaj' => $dokument->rodzaj,
        'dotyczy' => $dokument->dotyczy,
        'rodzaj_err' => '',
        'dotyczy_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['rodzaj'] = trim($_POST['rodzaj']);
        $data['dotyczy'] = trim($_POST['dotyczy']);

        $data['rodzaj_err'] = $this->validator->sprawdzDlugosc($data['rodzaj'], 4);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);

        if (empty($data['rodzaj_err']) &&
            empty($data['dotyczy_err'])) {

          $this->innyModel->edytujInnyDokument($data);
          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($dokument->id_sprawa, 11, $_SESSION['user_id'], 3, $id);

          $wiadomosc = "Dokument został zmieniony pomyślnie.";
            flash('sprawy_szczegoly', $wiadomosc);
            redirect('sprawy/szczegoly/'. $dokument->id_sprawa);
        }
      }

      $this->view('inne/edytuj', $data);
    }


    public function ajax_inny($id) {
       /*
        * Pobiera dane innego dokumentu i drukuje je w postaci json.
        * Zastosowanie do zapytania ajax.
        * Jeżeli dokument nie istnieje w miejsu id wstawiona zostaje wartość -1
        *
        * Funkcja nie obsługuje widoku.
        *
        * Parametry:
        *  - id => id pobieranego dokumentu
        * Zwraca:
        *  - echo json postaci: { id:, rodzaj:, itd... }
        */

       // tylko zalogowany
       sprawdzCzyPosiadaDostep(4,0);

       $pismo = $this->innyModel->pobierzInnyDokumentPoId($id);
       if ($pismo) {
         echo json_encode($pismo);
       } else {
         echo '{"id":"-1"}';
       }
    }



  }
