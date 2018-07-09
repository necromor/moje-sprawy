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
      $this->podmiotModel = $this->model('Podmiot');
      $this->metrykaModel = $this->model('Metryka');

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

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji pisma wychodzącego.
       *
       * W ramach edycji pisma użytkownik może zmienić tylko dane odbiorcy oraz treść dotyczy.
       T*
       * Prawdopodobna możliwość USTAWIENIA PISMA JAKO DECYZJA LUB POSTANOWIENIE, ale w osobnym widoku.
       * Nie będzie możliwości rezygnacji jeżeli pismo jest już decyzją lub postanowieniem.
       *
       * Oznaczenie sposobu odbioru i daty odbywa się w innym widoku.
       *
       * Pod pomyślnej edycji powrót następuje do zestawienia lub szczegółów sprawy w zależności skąd było wywołanie.
       *
       * Obsługuje widok: wychodzace/edytuj
       *
       * Parametry:
       *  - id => id pisma wychodzącego do zmiany
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

      $pismo = $this->wychodzacaModel->pobierzWychodzacaPoId($id);
      $podmioty = $this->podmiotModel->pobierzPodmioty();

      $data = [
        'title' => 'Zmień dane pisma wychodzącego',
        'id' => $id,
        'zrodlo' => $zrodlo,
        'sprawaId' => $pismo->sprawaId,
        'rok' => substr($pismo->utworzone, 0 ,4), //potrzebne do powrotu jak źródło w zestawieniu
        'czy_nowy' => 0,
        'podmiot_nazwa' => utworzIdNazwa($pismo->id_podmiot, $pismo->nazwa),
        'podmiot_adres' => $pismo->adres_1,
        'podmiot_poczta' => $pismo->adres_2,
        'dotyczy' => $pismo->dotyczy,
        'podmioty' => $podmioty,
        'podmiot_nazwa_err' => '',
        'podmiot_adres_err' => '',
        'podmiot_poczta_err' => '',
        'dotyczy_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // pola disabled nie wysyłają danych więc trzeba pobrać dane podmiotu
        if ($_POST['czyNowy'] == '0') {
          $podmiot = $this->podmiotModel->pobierzPodmiot($_POST['nazwaPodmiotu']);
          $nazwa = $podmiot->nazwa;
          $adres = $podmiot->adres_1;
          $poczta = $podmiot->adres_2;
        } else {
          $nazwa = trim($_POST['nazwaPodmiotu']);
          $adres = trim($_POST['adresPodmiotu']);
          $poczta = trim($_POST['pocztaPodmiotu']);
        }

        $data['czy_nowy'] = $_POST['czyNowy'];
        $data['podmiot_nazwa'] = $nazwa;
        $data['podmiot_adres'] = $adres;
        $data['podmiot_poczta'] = $poczta;
        $data['dotyczy'] = trim($_POST['dotyczy']);

        $data['podmiot_nazwa_err'] = $this->validator->sprawdzPodmiot($data['podmiot_nazwa'], 4, $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->validator->sprawdzDlugosc($data['podmiot_adres'], 6, $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->validator->sprawdzDlugosc($data['podmiot_poczta'], 6, $data['czy_nowy']);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);

        if (empty($data['podmiot_nazwa_err']) &&
            empty($data['podmiot_adres_err']) &&
            empty($data['podmiot_poczta_err']) &&
            empty($data['dotyczy_err'])) {

          // sprawdz czy nowy podmiot
          if ($data['czy_nowy'] == '1') {
            // przekształć dane na format podmiotu
            $podm = [
              'nazwa_podmiotu' => $data['podmiot_nazwa'],
              'adres_podmiotu' => $data['podmiot_adres'],
              'poczta_podmiotu' => $data['podmiot_poczta']
            ];
            // dodaj nowy podmiot
            if ($this->podmiotModel->dodajPodmiot($podm)) {
              $podmiot = $this->podmiotModel->pobierzDanePodmiotuPoNazwie($data['podmiot_nazwa']);
              // wstaw nazwę z id do danych
              $data['podmiot_nazwa'] = utworzIdNazwa($podmiot->id, $podmiot->nazwa);
            }
          }

          $id_pisma = $this->wychodzacaModel->edytujWychodzaca($data);
          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($pismo->sprawaId, 10, $_SESSION['user_id'], 2, $id);

          $wiadomosc = "Pismo wychodzące zostało zmienione pomyślnie.";
          if ($zrodlo == 'zestawienie') {
            flash('wychodzace_edytuj', $wiadomosc);
            redirect('wychodzace/zestawienie/'. substr($pismo->utworzone, 0, 4));
          } else {
            flash('sprawy_szczegoly', $wiadomosc);
            redirect('sprawy/szczegoly/'. $pismo->sprawaId);
          }
        }
      }

      $this->view('wychodzace/edytuj', $data);
    }

    public function odbior($id, $rodzaj) {
      /*
       * Obsługuje proces dodania daty i sposobu odbioru pisma wychodzącego.
       *
       * Pod pomyślnej edycji powrót następuje do zestawienia lub szczegółów sprawy w zależności skąd było wywołanie.
       *
       * Nie obsługuje widoku
       *
       * Parametry:
       *  - id => id pisma wychodzącego do zmiany
       *  - rodzaj => rodzaj sposobu odbioru: 1 - osobiście, 2 - poczta
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

      $pismo = $this->wychodzacaModel->pobierzWychodzacaPoId($id);
      $sposob = "odebrane osobiście";
      if ($rodzaj != '1') {
        $sposob = "wysłane pocztą";
      }

      $this->wychodzacaModel->oznaczWyslane($id, $rodzaj);
      $wiadomosc = "Pismo wychodzące zostało oznaczone jako $sposob";
      if ($zrodlo == 'zestawienie') {
        flash('wychodzace_edytuj', $wiadomosc);
        redirect('wychodzace/zestawienie/'. substr($pismo->utworzone, 0, 4));
      } else {
        flash('sprawy_szczegoly', $wiadomosc);
        redirect('sprawy/szczegoly/'. $pismo->sprawaId);
      }

    }

    public function szukaj() {
       /*
        * Obsługuje proces wyszukiwania pism wychodzących.
        * Pisma można wyszukać na podstawie jednego lub kilku wypełnioych pól.
        * Lista kryteriów, po których można wyszukać pisma:
        *  - znak sprawy
        *  - data pisma
        *  - nazwa odbiorcy
        *  - treść dotyczy
        * Funkcja nie sprawdza co znajduje się w polach.
        * Na podstawie otrzymanych danych zwraca się do modelu o podanie wyników.
        *
        * Obsługuje widok: wychodzace/szukaj
        */

       // tylko zalogowany, nie admin
       sprawdzCzyPosiadaDostep(4,0);

       $data = [
         'title' => 'Szukaj pism wychodzących',
         'znak' => '',
         'data_pisma' => '',
         'nazwa' => '',
         'dotyczy' => '',
         'czy_wyniki' => -1, // -1 oznacza, że nie było jeszcze wyszukiwania - pokaż stronę główną wyszukiwania
         'pisma' => []
       ];

       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


         $data['znak'] = trim($_POST['znak']);
         $data['data_pisma'] = trim($_POST['data_pisma']);
         $data['nazwa'] = trim($_POST['nazwa']);
         $data['dotyczy'] = trim($_POST['dotyczy']);

         $pisma = $this->wychodzacaModel->szukajWychodzace($data);

         // dodaj szczególy pisma, które będa rozwinięte w widoku po kliknięciu guzika
         foreach ($pisma as $pismo) {
           $pismo->szczegoly = $this->tworzHtmlSzczegoly($pismo);
         }

         $data['pisma'] = $pisma;
         $data['czy_wyniki'] = count($data['pisma']);

       }

       $this->view('wychodzace/szukaj', $data);
    }

    private function tworzHtmlSzczegoly($pismo) {
      /*
       * Funkcja pomocnicza, która tworzy html dla danych pisma
       * Dodatkowo umieszcza informację dotyczącą decyzji lub postanowienia jeżeli istnieją.
       *
       * Parametry:
       *  - pismo => obiekt korespondencji
       * Zwraca:
       *  - string - html z danymi danego dokumentu
       */

      $html = '<div class="card border-secondary">
               <div class="card-body">
               <div class="row">';
      $html.= '<p class="col-12"><span class="badge badge-dark p-2">dotyczy:</span> ' . $pismo->dotyczy . '</p>';
      if ($pismo->data_wyjscia != NULL) {
        $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">data wyjścia:</span> ';
        if ($pismo->sposob_wyjscia == '1') {
          $html.='odebrane osobiście dnia: ';
        } else {
          $html.='wysłane dnia: ';
        }
        $html.= substr($pismo->data_wyjscia, 0, 10);
        $html.= '</p>';
      } else {
        $html.= '<div class="col-12 py-sm-3">
                 <span class="badge badge-dark p-2 mr-3">oznacz sposób odbioru:</span> 
                 <div class="btn-group" role="group" aria-label="Oznaczenie sposobu wysyłki">
                   <a href="' . URLROOT . '/wychodzace/odbior/' . $pismo->id . '/1" class="btn btn-primary">osobiście</a>
                   <a href="' . URLROOT . '/wychodzace/odbior/' . $pismo->id . '/2" class="btn btn-secondary">pocztą</a>
                 </div>
                 </div>';
      }
      if ($pismo->decyzjaId) {
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">numer decyzji:</span> <a href="' . URLROOT . '/decyzje/edytuj/' . $pismo->decyzjaId . '" title="Zmień dane decyzji">' . $pismo->decyzjaNumer . '</a></p>';
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">treść decyzji:</span> ' . $pismo->decyzjaDotyczy . '</p>';
      }
      if ($pismo->postanowienieId) {
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">numer postanowienia:</span> <a href="' . URLROOT . '/postanowienia/edytuj/' . $pismo->postanowienieId . '" title="Zmień dane postanowienia">' . $pismo->postanowienieNumer . '</a></p>';
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">treść postanowienia:</span> ' . $pismo->postanowienieDotyczy . '</p>';
      }
      $html.='</div></div></div>';
      return $html;
    }


  }
