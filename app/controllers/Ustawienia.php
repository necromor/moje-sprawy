<?php
  /*
   *  Kontroler Ustawienia odpowiedzialny jest za obsługę części modelu Admin.
   *  Obecnie jest wykorzystywany tylko do ustawienia terminu ważności hasła.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Ustawienia extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->adminModel = $this->model('Admin');
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

    public function zmien_termin() {
      /*
       * Obsługuje proces zmiany temrinu ważności hasła.
       *
       * Termin ważności wyrażony jest w dniach.
       * Za poprawne wprowadzenie danych odpowiada widok (liczbowe pole formularza).
       *
       *
       * Obsługuje widok: ustawienia/zmien_termin
       */

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

      // zawsze będzie - docelowy ustawiany przy tworzeniu konta admina
      $termin = $this->adminModel->pobierzTerminWaznosciHasla();

      $data = [
        'title' => 'Ustaw termin ważności hasła',
        'termin' => $termin
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data['termin'] = trim($_POST['termin']);

        $this->adminModel->ustawTerminWaznosciHasla($data['termin']);
        $wiadomosc = "Ważność hasła została zmieniona na " . $data['termin'] ." dni.";
        flash('zmien_termin', $wiadomosc);
        redirect('ustawienia/zmien_termin');

      } else {

        $this->view('ustawienia/zmien_termin', $data);
      }
    }


  }


?>
