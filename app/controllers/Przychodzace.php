<?php
  /*
   *  Kontroler Przychodzace odpowiedzialny jest za obsługę modelu Przychodzaca z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Przychodzace extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

      $this->przychodzacaModel = $this->model('Przychodzaca');
      $this->podmiotModel = $this->model('Podmiot');
      $this->pracownikModel = $this->model('Pracownik');
      $this->sprawaModel = $this->model('Sprawa');
      $this->jrwaModel = $this->model('JrwaM');

      $this->validator = new Validator();
    }

    public function zestawienie($rok) {
      /*
       * Tworzy zestaw obiektów Przychodzące
       *
       * Obsługuje widok: przychodzace/zestawienie/rok
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        redirect('przychodzace/zestawienie/' . $_POST['rok']);
      }

      $pisma = $this->przychodzacaModel->pobierzPrzychodzace($rok);
      // zamień id pracownika na imie i nazwisko
      // jeżeli korespondencja nie jest fakturą
      foreach($pisma as $pismo) {
        if($pismo->id_pracownik != 0) {
          $pismo->id_pracownik = $this->pracownikModel->pobierzImieNazwisko($pismo->id_pracownik);
        }
      }

      $data = [
        'title' => 'Zestawienie korespondencji przychodzącej',
        'pisma' => $pisma,
        'rok' => $rok
      ];

      $this->view('przychodzace/zestawienie', $data);
    }

    public function faktury($rok, $id=0) {
      /*
       * Tworzy zestaw obiektów Faktury, który jest podzbiorem Przychodzącej
       * Kwoty zamieniane są na format 000.000,00
       *
       * Obsługuje widok: przychodzace/faktury/rok
       */

      // tylko zalogownay sekretariat lub księgowość
      sprawdzCzyPosiadaDostep(1,0);

      // sprawdź czy nastąpiła zmiana roku
      // jeżeli tak to przekieruj 
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        // sprawdź czy przesłany został wykonawca
        if (isset($_POST['wystawca']) && trim($_POST['wystawca']) != '') {

          $idp = pobierzIdNazwy($_POST['wystawca']);
          redirect('przychodzace/faktury/' . $_POST['rok'] . '/' . $idp);
        } else {
          redirect('przychodzace/faktury/' . $_POST['rok']);
        }
      }

      $faktury = $this->przychodzacaModel->pobierzFaktury($rok, $id);
      $listaPodmiotow = $this->podmiotModel->pobierzPodmioty();
      $wybrany_podmiot = '';

      if ($id != 0) {
        $podmiot = $this->podmiotModel->pobierzDanePodmiotu($id);
        $wybrany_podmiot = utworzIdNazwa($podmiot->id, $podmiot->nazwa);
      }

      // oblicz sumę i zmień formatowanie liczb
      $suma = 0;
      foreach($faktury as $faktura) {
        $suma+= $faktura->kwota;
        if ($faktura->kwota < 0) {
          $faktura->ujemna = true;
        } else {
          $faktura->ujemna = false;
        }
        $faktura->kwota = formatujKwote($faktura->kwota);
      }

      $data = [
        'title' => 'Zestawienie faktur',
        'faktury' => $faktury,
        'rok' => $rok,
        'podmioty' => $listaPodmiotow,
        'wybrany' => $wybrany_podmiot,
        'suma' => $suma
      ];

      $this->view('przychodzace/faktury', $data);
    }

    public function moje() {
      /*
       * Podstawowa strona powitalna dla każdego pracownika.
       * Zawiera zestawienie zadekretowanych do niego/niej pism przychodzących,
       * które nie przypisane są jeszcze do żadnej sprawy lub oznaczona jako ad/acta.
       *
       * Umożliwia oznaczenie pisma (pism?) jako ad/acta.
       *
       * DO ROZPATRZENIA: możliwość przypisania do sprawy
       *
       * Obsługuje widok: przychodzace/moje
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $pisma = $this->przychodzacaModel->pobierzPismaDoZrobienia($_SESSION['user_id']);
      $sprawy = $this->sprawaModel->ostatniaAktywnosc($_SESSION['user_id'], 20);

      $data = [
        'title' => 'Moje pisma',
        'pisma' => $pisma,
        'sprawy' => $sprawy
      ];

      $this->view('przychodzace/moje', $data);
    }

   public function adacta($id) {
     /*
      * Obsługuje proces oznaczenia pisma jako ad acta.
      * Pismo oznaczone ad acta przypisywane jest tylko do kategorii JRWA.
      * Nie interesuje nas data, ani sprawa.
      *
      * Obsługuje widok: przychodzace/adacta/id
      *
      * Parametry:
      *  - id => id pisma przeznaczonego do oznaczenia jako ad acta
      */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $pismo = $this->przychodzacaModel->pobierzPrzychodzacaPoId($id);
      $jrwaLista = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Oznacz pismo jako ad acta',
        'id' => $id,
        'pismo' => $pismo,
        'jrwaLista' => $jrwaLista,
        'jrwa' => '',
        'jrwa_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['jrwa'] = $_POST['jrwa'];
        $data['jrwa_err'] = $this->validator->sprawdzJrwa($data['jrwa']);

        if (empty($data['jrwa_err'])) {
          // zamień numer jrwa na jego id
          $nr_jrwa = $this->jrwaModel->pobierzJrwaPoNumerze($data['jrwa']);
          $jrwa = $nr_jrwa->id;

          //ZAIMPLEMENTOWAĆ SPRAWDZENIE CZY PISMO MOŻNA OZNACZYĆ AD ACTA

          $this->przychodzacaModel->oznaczAA($id, $jrwa);

          $wiadomosc = "Pismo o numerze rejestru <strong>$pismo->nr_rejestru</strong> zostało przypisane do numeru jrwa <strong>$nr_jrwa->numer</strong> jako ad acta.";
          flash('moje_info', $wiadomosc);
          redirect('przychodzace/moje');
        }

      }
      $this->view('przychodzace/adacta', $data);
   }

   public function dodaj() {
      /*
       * Obsługuje proces dodawania nowej korespondencji przychodzącej.
       * Działa w dwóch trybach: wyświetlanie formularza, obsługa formularza.
       * Tryb wybierany jest w zależności od metody dostępu do strony: POST czy GET.
       * POST oznacza, że formularz został wysłany, każda inna forma dostępu powoduje
       * wyświetlenie formularza.
       *
       * Tryb wyświetlania formularza może mieć dwa stany:
       * czysty - gdy wyświetlany jest formularz po raz pierwszy
       * brudny - gdy wyświetlany jest formularz z błędami
       * Tryb czysty zawiera puste dane, tryb brudny przechowuje dane przesłane przez
       * użytkownika i umieszcza je w stosownych polach formularza
       *
       * Tryb obsługi odpowiada za sprawdzenie wprowadzonych danych (szczegóły w
       * indywidualnych funkcjach sprawdzających) i w zależności od tego czy są błędy
       * wywołuje metodę modelu dodawania pracownika lub wyświetla brudny formularz.
       *
       * Obsługuje widok: przychodzace/dodaj
       */

      // tylko zalogowany sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $listaPodmiotow = $this->podmiotModel->pobierzPodmioty();
      $listaPracownikow = $this->pracownikModel->pobierzPracownikow();

      $data = [
        'title' => 'Dodaj korespondencję przychodzącą',
        'podmioty' => $listaPodmiotow,
        'pracownicy' => $listaPracownikow,
        'czy_nowy' => '0',
        'czy_faktura' => '0',
        'podmiot_nazwa' => '',
        'podmiot_adres' => '',
        'podmiot_poczta' => '',
        'znak' => '',
        'data_pisma' => '',
        'data_wplywu' => '',
        'dotyczy' => '',
        'liczba_zalacznikow' => '0',
        'dekretacja' => '',
        'kwota' => '0.00',
        'podmiot_nazwa_err' => '',
        'podmiot_adres_err' => '',
        'podmiot_poczta_err' => '',
        'znak_err' => '',
        'data_pisma_err' => '',
        'data_wplywu_err' => '',
        'dotyczy_err' => '',
        'liczba_zalacznikow_err' => '',
        'dekretacja_err' => '',
        'kwota_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // pola disabled nie wysyłają danych więc trzeba pobrać dane podmiotu
        if ($_POST['czyNowy'] == '0') {
          $podmiot = $this->pobierzPodmiot($_POST['nazwaPodmiotu']);
          $nazwa = $podmiot->nazwa;
          $adres = $podmiot->adres_1;
          $poczta = $podmiot->adres_2;
        } else {
          $nazwa = trim($_POST['nazwaPodmiotu']);
          $adres = trim($_POST['adresPodmiotu']);
          $poczta = trim($_POST['pocztaPodmiotu']);
        }

        $data['podmiot_nazwa'] = $nazwa;
        $data['podmiot_adres'] = $adres;
        $data['podmiot_poczta'] = $poczta;

        $data['czy_nowy'] = $_POST['czyNowy'];
        $data['znak'] = trim($_POST['znak']);
        $data['data_pisma'] = trim($_POST['dataPisma']);
        $data['data_wplywu'] = trim($_POST['dataWplywu']);
        $data['dotyczy'] = trim($_POST['dotyczy']);
        $data['czy_faktura'] = $_POST['czyFaktura'];
        $data['liczba_zalacznikow'] = trim($_POST['liczbaZalacznikow']);
        $data['dekretacja'] = trim($_POST['dekretacja']);
        $data['kwota'] = trim($_POST['kwota']);

        $data['podmiot_nazwa_err'] = $this->validator->sprawdzPodmiot($data['podmiot_nazwa'], $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->validator->sprawdzDlugosc($data['podmiot_adres'], 4, $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->validator->sprawdzDlugosc($data['podmiot_poczta'], 8, $data['czy_nowy']);
        $data['znak_err'] = $this->validator->sprawdzDlugosc($data['znak'], 2);
        $data['data_pisma_err'] = $this->validator->sprawdzDatePisma($data['data_pisma']);
        $data['data_wplywu_err'] = $this->validator->sprawdzDateWplywu($data['data_wplywu']);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);
        $data['liczba_zalacznikow_err'] = $this->validator->sprawdzDlugosc($data['liczba_zalacznikow'], 1);
        $data['dekretacja_err'] = $this->validator->sprawdzDekretacja($data['dekretacja'], $data['czy_faktura']);
        $data['kwota_err'] = $this->validator->sprawdzDlugosc($data['kwota'], 1, $data['czy_faktura']);

        // Dodaj do bazy danych gdy nie ma błędów
        if (empty($data['podmiot_nazwa_err']) &&
            empty($data['podmiot_adres_err']) &&
            empty($data['podmiot_poczta_err']) &&
            empty($data['znak_err']) &&
            empty($data['data_pisma_err']) &&
            empty($data['data_wplywu_err']) &&
            empty($data['dotyczy_err']) &&
            empty($data['liczba_zalacznikow_err']) &&
            empty($data['dekretacja_err']) &&
            empty($data['kwota_err'])) {

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

          $numery = $this->przychodzacaModel->dodajPrzychodzaca($data);

          // utwórz wiadomość zwrotną w zależności do zostało dodane
          $wiadomosc = "Korespondencja dodana pomyślnie z numerem rejestru: <strong>" . $numery['nr_rejestru'] . "</strong>";
          if ($data['czy_faktura'] == '1') {
            $wiadomosc.= "<br>Numer w rejestrze faktur: <strong>" . $numery['nr_rejestru_faktur'] . "</strong>";
          }
          flash('korespondencja_dodaj', $wiadomosc);
          redirect('przychodzace/dodaj');
        } else {
          $this->view('przychodzace/dodaj', $data);
        }
      } else {
        // musi być else gdyż inaczej nie działa wiadomość flash

        $this->view('przychodzace/dodaj', $data);
      }
   }


   public function edytuj($id) {
      /*
       * Obsługuje proces edycji istniejącej korespondencji przychodzącej.
       * Sposób działania jest identyczy jak funkcji dodaj() z niewielką różnicą
       * w trybie czystym - do pól formularza wprowadzane są dane edytowanej korespondencji.
       *
       * Obsługuje widok: przychodzace/edytuj/id
       *
       * Parametry:
       *  - id => id edytowanej korespondencji
       */

      // tylko zalogownay sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $pismo = $this->przychodzacaModel->pobierzPrzychodzacaPoId($id);

      // zamień dane pracownika jeżeli to pismo nie faktura
      if($pismo->id_pracownik != 0) {
        $imie_nazwisko = $this->pracownikModel->pobierzImieNazwisko($pismo->id_pracownik);
        $pismo->id_pracownik = utworzIdNazwa($pismo->id_pracownik, $imie_nazwisko);
      }

      $listaPodmiotow = $this->podmiotModel->pobierzPodmioty();
      $listaPracownikow = $this->pracownikModel->pobierzPracownikow();

      $data = [
        'title' => 'Zmień dane korespondencji przychodzącej',
        'podmioty' => $listaPodmiotow,
        'pracownicy' => $listaPracownikow,
        'id' => $id,
        'czy_nowy' => '0',
        'czy_faktura' => $pismo->czy_faktura,
        'podmiot_nazwa' => utworzIdNazwa($pismo->id_podmiot, $pismo->nazwa),
        'podmiot_adres' => $pismo->adres_1,
        'podmiot_poczta' => $pismo->adres_2,
        'znak' => $pismo->znak,
        'data_pisma' => $pismo->data_pisma,
        'data_wplywu' => $pismo->data_wplywu,
        'dotyczy' => $pismo->dotyczy,
        'liczba_zalacznikow' => $pismo->liczba_zalacznikow,
        'dekretacja' => $pismo->id_pracownik,
        'kwota' => $pismo->kwota,
        'podmiot_nazwa_err' => '',
        'podmiot_adres_err' => '',
        'podmiot_poczta_err' => '',
        'znak_err' => '',
        'data_pisma_err' => '',
        'data_wplywu_err' => '',
        'dotyczy_err' => '',
        'liczba_zalacznikow_err' => '',
        'dekretacja_err' => '',
        'kwota_err' => ''
      ];


      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // pola disabled nie wysyłają danych więc trzeba pobrać dane podmiotu
        if ($_POST['czyNowy'] == '0') {
          $podmiot = $this->pobierzPodmiot($_POST['nazwaPodmiotu']);
          $nazwa = $podmiot->nazwa;
          $adres = $podmiot->adres_1;
          $poczta = $podmiot->adres_2;
        } else {
          $nazwa = trim($_POST['nazwaPodmiotu']);
          $adres = trim($_POST['adresPodmiotu']);
          $poczta = trim($_POST['pocztaPodmiotu']);
        }

        $data['podmiot_nazwa'] = $nazwa;
        $data['podmiot_adres'] = $adres;
        $data['podmiot_poczta'] = $poczta;

        $data['czy_nowy'] = $_POST['czyNowy'];
        $data['znak'] = trim($_POST['znak']);
        $data['data_pisma'] = trim($_POST['dataPisma']);
        $data['data_wplywu'] = trim($_POST['dataWplywu']);
        $data['dotyczy'] = trim($_POST['dotyczy']);
        $data['czy_faktura'] = $_POST['czyFaktura'];
        $data['liczba_zalacznikow'] = trim($_POST['liczbaZalacznikow']);
        $data['dekretacja'] = trim($_POST['dekretacja']);
        $data['kwota'] = trim($_POST['kwota']);

        $data['podmiot_nazwa_err'] = $this->validator->sprawdzPodmiot($data['podmiot_nazwa'], $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->validator->sprawdzDlugosc($data['podmiot_adres'], 4, $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->validator->sprawdzDlugosc($data['podmiot_poczta'], 8, $data['czy_nowy']);
        $data['znak_err'] = $this->validator->sprawdzDlugosc($data['znak'], 2);
        $data['data_pisma_err'] = $this->validator->sprawdzDatePisma($data['data_pisma']);
        $data['data_wplywu_err'] = $this->validator->sprawdzDateWplywu($data['data_wplywu']);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);
        $data['liczba_zalacznikow_err'] = $this->validator->sprawdzDlugosc($data['liczba_zalacznikow'], 1);
        $data['dekretacja_err'] = $this->validator->sprawdzDekretacja($data['dekretacja'], $data['czy_faktura']);
        $data['kwota_err'] = $this->validator->sprawdzDlugosc($data['kwota'], 1, $data['czy_faktura']);

        // Dodaj do bazy danych gdy nie ma błędów
        if (empty($data['podmiot_nazwa_err']) &&
            empty($data['podmiot_adres_err']) &&
            empty($data['podmiot_poczta_err']) &&
            empty($data['znak_err']) &&
            empty($data['data_pisma_err']) &&
            empty($data['data_wplywu_err']) &&
            empty($data['dotyczy_err']) &&
            empty($data['liczba_zalacznikow_err']) &&
            empty($data['dekretacja_err']) &&
            empty($data['kwota_err'])) {

          // sprawdz czy nowy podmiot
          if ($data['czy_nowy'] == '1') {
            // przekszałć dane na format podmiotu
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
          $this->przychodzacaModel->edytujPrzychodzaca($data);

          $wiadomosc = "Dane korespondencji zostały zmienione pomyślnie.";
          flash('korespondencja_edytuj', $wiadomosc);
          redirect('przychodzace/zestawienie/'. date("Y"));
        }
      }

      $this->view('przychodzace/edytuj', $data);
   }


   public function szukaj() {
      /*
       * Obsługuje proces wyszukiwania korespondencji przychodzącej.
       * Korespondencję można wyszukać na podstawie jednego lub kilku wypełnioych pól.
       * Lista kryteriów, po których można wyszukać korespondencję:
       *  - numer rejestru
       *  - znak pisma
       *  - data pisma / data wpływu
       *  - nazwa nadawcy
       *  - treść dotyczy
       * Funkcja nie sprawdza co znajduje się w polach.
       * Na podstawie otrzymanych danych zwraca się do modelu o podanie wyników.
       *
       * Ze względu na ograniczenia w dostępie do faktur tylko dla księgowości i sekretariatu
       * szczegóły faktur będa ukryte dla zwykłych pracowników.
       *
       * Alternatywą jest ograniczenie wyszukiwania tylko do pism, ale to może rodzić niezrozumienie
       * gdy pojawią się dziury w numerach rejestrów.
       *
       * Obsługuje widok: przychodzace/szukaj
       */

      // tylko zalogowany, nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $data = [
        'title' => 'Szukaj korespondencji przychodzącej',
        'nr_rej' => '',
        'znak' => '',
        'data_pisma' => '',
        'data_wplywu' => '',
        'nazwa' => '',
        'dotyczy' => '',
        'czy_wyniki' => -1, // -1 oznacza, że nie było jeszcze wyszukiwania - pokaż stronę główną wyszukiwania
        'pisma' => []
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


        $data['nr_rej'] = trim($_POST['nr_rej']);
        $data['znak'] = trim($_POST['znak']);
        $data['data_pisma'] = trim($_POST['data_pisma']);
        $data['data_wplywu'] = trim($_POST['data_wplywu']);
        $data['nazwa'] = trim($_POST['nazwa']);
        $data['dotyczy'] = trim($_POST['dotyczy']);

        $pisma = $this->przychodzacaModel->szukajPrzychodzace($data);

        // dodaj szczególy pisma, które będa rozwinięte w widoku po kliknięciu guzika
        foreach ($pisma as $pismo) {
          $pismo->szczegoly = $this->tworzHtmlSzczegoly($pismo);
        }

        $data['pisma'] = $pisma;
        $data['czy_wyniki'] = count($data['pisma']);

      }

      $this->view('przychodzace/szukaj', $data);
   }



   /*
    * FUNKCJE POMOCNICE
    */

   private function pobierzPodmiot($nazwa) {
     /*
      * Funkcja, która pobiera dane podmiotu po podanej nazwie i podmienia
      * nazwę na format id# nazwa, jaki jest wykorzystywany w formularzach dodawania/edycji.
      * Nie jest to dokładnie taka funkcja jak w modelu,
      * gdyż zabezpiecza ona wypadek, gdy podmiot nie istnieje.
      *
      * Parametry:
      *  - nazwa => nazwa szukanego podmiotu
      * Zwraca:
      *  - obiekt podmiotu
      */

     $idp = pobierzIdNazwy($nazwa);

     // sprawdź czy istnieje taki podmiot
     if ($this->podmiotModel->czyIstniejePodmiot($idp)) {
       $podmiot = $this->podmiotModel->pobierzDanePodmiotu($idp);
       // na wypadek gdyby id było ok a nazwa zmieniona
       // przywróc tą z bazy danych
       $podmiot->nazwa = utworzIdNazwa($podmiot->id, $podmiot->nazwa);
     } else {
       // zwróć pusty obiekt
       $podmiot = (object) ['nazwa' => '', 'adres_1' => '', 'adres_2' => ''];
     }
     return $podmiot;
   }

   private function tworzHtmlSzczegoly($pismo) {
     /*
      * Funkcja pomocnicza, która tworzy html dla danych dokumentu
      * Elementy szczegółowe w zależności od rodzaju: pismo czy faktura 
      * są pobierane z pomocniczych funkcji.
      *
      * Użytkownicy z poziomem dostępu nie sekretariat lub księgowość
      * nie mają dostępu do szczegółów faktur.
      *
      * Parametry:
      *  - pismo => obiekt korespondencji
      * Zwraca:
      *  - string - html z danymi danego dokumentu
      */

     // zabezpieczenie widoku faktur
     if ($_SESSION['poziom'] > 1 && $pismo->czy_faktura == '1') {
       return "<p>Nie masz uprawnień do oglądania szczegółów faktur!</p>";
     }

     $html = '<div class="card border-secondary">
              <div class="card-body">
              <div class="row">';
     $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">data pisma:</span> ' . $pismo->data_pisma. '</p>';
     $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">data wpływu:</span> ' . $pismo->data_wplywu. '</p>';
     $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">dotyczy:</span> ' . $pismo->dotyczy . '</p>';
     if ($pismo->czy_faktura == '0') {
       $html.= $this->tworzHtmlPismo($pismo);
     } else {
       $html.= $this->tworzHtmlFaktura($pismo);
     }
     $html.='</div></div></div>';
     return $html;
   }

   private function tworzHtmlPismo($pismo) {
     /*
      * Funkcja pomocnicza, która tworzy html dla danych pisma przychodzącego, tj:
      * liczba załączników i dekretacja
      *
      * Dodatkowo informacje czy ad acta lub przypisane do sprawy.
      *
      * Parametry:
      *  - pismo => obiekt pisma przychodzącego
      * Zwraca:
      *  - string - html z danymi pisma przychodzącego
      */

     $imie_nazwisko = $this->pracownikModel->pobierzImieNazwisko($pismo->id_pracownik);
     $jrwa = $this->przychodzacaModel->pobierzJrwaAA($pismo->id);
     $sprawa = $this->przychodzacaModel->pobierzSprawePrzychodzacego($pismo->id);

     $html = '<p class="col-sm col-12"><span class="badge badge-dark p-2">liczba załączników:</span> ' . $pismo->liczba_zalacznikow . '</p>';
     $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">zadekretowane do:</span> ' . $imie_nazwisko . '</p>';
     if ($jrwa) {
       $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">pismo oznaczone jako ad acta w grupie jrwa:</span> ' . $jrwa->numer . '</p>';
     }
     if ($sprawa) {
       $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">pismo przypisane do sprawy:</span> 
              <a href="' . URLROOT . '/sprawy/szczegoly/' . $sprawa->id . '" title="Zobacz szczegóły sprawy">' . $sprawa->znak . '</a></p>';
     }
     return $html;
   }

   private function tworzHtmlFaktura($pismo) {
     /*
      * Funkcja pomocnicza, która tworzy html dla danych faktury, tj:
      * kwota i numer w rejestrze faktur
      *
      * Parametry:
      *  - pismo => obiekt faktury
      * Zwraca:
      *  - string - html z danymi faktury
      */

     $html = '<p class="col-sm col-12"><span class="badge badge-dark p-2">kwota faktury:</span> ' . $pismo->kwota . '</p>';
     $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">numer w rejestrze faktur:</span> ' . $pismo->nr_rejestru_faktur . '</p>';
     return $html;
   }

  }
