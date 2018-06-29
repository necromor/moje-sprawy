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
      $this->metrykaModel = $this->model('Metryka');
      $this->pracownikModel = $this->model('Pracownik');
      $this->przychodzacaModel = $this->model('Przychodzaca');
      $this->jrwaModel = $this->model('JrwaM');
      $this->podmiotModel = $this->model('Podmiot');
      $this->wychodzacaModel = $this->model('Wychodzaca');
      $this->decyzjaModel = $this->model('Decyzja');
      $this->postanowienieModel = $this->model('Postanowienie');
      $this->innyModel = $this->model('Inny');

      $this->validator = new Validator();
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
      $metryka = $this->metrykaModel->pobierzMetrykeSprawy($id);
      $zakonczona = $this->sprawaModel->czyZakonczona($id);

      // podmiana danych w metryce na bardziej czytelne
      $lp = 1;
      foreach($metryka as $m) {
        // zastąp id liczbą porządkową
        $m->id = $lp;
        $lp++;

        // zastąp id_pracownik imieniem i nazwiskiem
        $m->id_pracownik = $this->pracownikModel->pobierzImieNazwisko($m->id_pracownik);

        // zastąp id_dokumentu unikalnym oznaczeniem
        // 0 - brak dokumentu
        // 1 - przychodzące
        // 2 - wychodzące
        // 3 - inny dokument
        switch ($m->rodzaj_dokumentu) {
          case 1:
            $dokument = $this->przychodzacaModel->pobierzPrzychodzacaPoId($m->id_dokument);
            $m->dokument = 'p' . strtotime($dokument->utworzone);
            $m->szczegoly = $this->tworzHtmlDokumentu($m->rodzaj_dokumentu, $dokument);
            break;
          case 2:
            $dokument = $this->wychodzacaModel->pobierzWychodzacaPoId($m->id_dokument);
            $m->dokument = 'w' . strtotime($dokument->utworzone);
            $m->szczegoly = $this->tworzHtmlDokumentu($m->rodzaj_dokumentu, $dokument);
            break;
          case 3:
            $dokument = $this->innyModel->pobierzInnyDokumentPoId($m->id_dokument);
            $m->dokument = 'i' . strtotime($dokument->utworzone);
            $m->szczegoly = $this->tworzHtmlDokumentu($m->rodzaj_dokumentu, $dokument);
            break;
          default:
            $m->dokument = '=====';
        }

      }

      $data = [
        'title' => 'Szczegóły sprawy ' . $sprawa->znak,
        'id' => $id,
        'zakonczona' => $zakonczona,
        'sprawa' => $sprawa,
        'metryka' => $metryka
      ];

      $this->view('sprawy/szczegoly', $data);
    }


    public function dodaj() {
      /*
       * Obsługuje proces dodawania nowej sprawy.
       * Działa w dwóch trybach: wyświetlanie formularza, obsługa formularza.
       * Tryb wybierany jest w zależności od metody dostępu do strony:
       * POST czy GET.
       * POST oznacza, że formularz został wysłany,
       * każda inna forma dostępu powoduje wyświetlenie formularza.
       *
       * Tryb wyświetlania formularza może mieć dwa stany:
       * czysty - gdy wyświetlany jest formularz po raz pierwszy
       * brudny - gdy wyświetlany jest formularz z błędami
       * Tryb czysty zawiera puste dane, tryb brudny przechowuje dane przesłane przez
       * użytkownika i umieszcza je w stosownych polach formularza
       *
       * Tryb obsługi odpowiada za sprawdzenie wprowadzonych danych 
       * i w zależności od tego czy są błędy wywołuje metodę modelu dodawania 
       * sprawy lub wyświetla brudny formularz.
       * Sprawdzanie poprawności wprowadzonych danych polega jedynie
       * na sprawdzeniu czy pola nie są puste.
       *
       * Obsługuje widok: sprawy/dodaj
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $jrwaLista = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Zarejestruj nową sprawę',
        'jrwaLista' => $jrwaLista,
        'jrwa' => '',
        'temat' => '',
        'jrwa_err' => '',
        'temat_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data['jrwa'] = trim($_POST['jrwa']);
        $data['temat'] = trim($_POST['temat']);

        $data['jrwa_err'] = $this->validator->sprawdzJrwa($data['jrwa']);
        $data['temat_err'] = $this->validator->sprawdzDlugosc($data['temat'], 10);

        if (empty($data['jrwa_err']) && empty($data['temat_err'])) {

          // zamień numer jrwa na jego id
          $nr_jrwa = $this->jrwaModel->pobierzJrwaPoNumerze($data['jrwa']);
          $jrwa = $nr_jrwa->id;

          // utwórz znak sprawy
          $znak = $this->utworzZnakSprawy($jrwa);

          $this->sprawaModel->dodajSprawe($znak, $jrwa, $data['temat']);

          // ustal id dodanej sprawy
          // aby przekierować użytkownika
          $sprawa = $this->sprawaModel->pobierzSprawePoZnaku($znak);

          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($sprawa->id, 0, $_SESSION['user_id']);

          $wiadomosc = "Sprawa została zarejestrowana poprawnie";
          flash('sprawy_szczegoly', $wiadomosc);
          redirect('sprawy/szczegoly/'.$sprawa->id);
        } else {
          // brudny
          $this->view('sprawy/dodaj', $data);
        }

      } else {

        // czysty
        $this->view('sprawy/dodaj', $data);
      }
    }

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji tematu sprawy.
       * Sposób działania jest identyczy jak funkcji dodaj() z niewielką różnicą
       * w trybie czystym - do pól formularza wprowadzane są dane edytowanej sprawy.
       *
       * Obsługuje widok: sprawy/edytuj/id
       *
       * Parametry:
       *  - id => id edytowanej sprawy
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // czy sprawa nie jest zakończona
      $this->sprawdzCzyZakonczona($id);

      $sprawa = $this->sprawaModel->pobierzSprawePoId($id);

      $data = [
        'title' => 'Zmień temat sprawy '. $sprawa->znak,
        'id' => $id,
        'temat' => $sprawa->temat,
        'temat_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data['temat'] = trim($_POST['temat']);

        $data['temat_err'] = $this->validator->sprawdzDlugosc($data['temat'], 10);

        if (empty($data['temat_err'])) {

          $this->sprawaModel->zmienTemat($id, $data['temat']);

          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($id, 9, $_SESSION['user_id']);

          $wiadomosc = "Temat sprawy został zmieniony pomyślnie.";
          flash('sprawy_szczegoly', $wiadomosc);
          redirect('sprawy/szczegoly/'.$id);
        } else {
          // brudny
          $this->view('sprawy/edytuj', $data);
        }

      } else {

        // czysty
        $this->view('sprawy/edytuj', $data);
      }
    }

    public function zakoncz($id) {
      /*
       * Obsługuje proces zakończenia sprawy.
       *
       * Nie obsługuje żadnego widoku.
       * Adres wywołania sprawy/zakoncz/id
       *
       * Parametry:
       *  - id => id sprawy do zakończenia
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // nie kończ zakończonej sprawy
      if ($this->sprawaModel->czyZakonczona($id)) {
        $wiadomosc = "Sprawa została zakończona wcześniej!";
        flash('sprawy_szczegoly', $wiadomosc);
        redirect('sprawy/szczegoly/'.$id);
      } else {
        $this->sprawaModel->zakonczSprawe($id);
        $this->metrykaModel->dodajMetryke($id, 99, $_SESSION['user_id']);

        $wiadomosc = "Sprawa została zakończona pomyślnie.";
        flash('sprawy_szczegoly', $wiadomosc);
        redirect('sprawy/szczegoly/'.$id);
      }

    }

    public function wznow($id) {
      /*
       * Obsługuje proces wznowienia sprawy.
       *
       * Nie obsługuje żadnego widoku.
       * Adres wywołania sprawy/wznow/id
       *
       * Parametry:
       *  - id => id sprawy do wznowienia
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // nie wznawiaj nie zakończonej sprawy
      if (!$this->sprawaModel->czyZakonczona($id)) {
        $wiadomosc = "Sprawa nie jest zakończona!";
        flash('sprawy_szczegoly', $wiadomosc);
        redirect('sprawy/szczegoly/'.$id);
      } else {
        $this->sprawaModel->wznowSprawe($id);
        $this->metrykaModel->dodajMetryke($id, 98, $_SESSION['user_id']);

        $wiadomosc = "Sprawa została wznowiona pomyślnie.";
        flash('sprawy_szczegoly', $wiadomosc);
        redirect('sprawy/szczegoly/'.$id);
      }

    }

    public function wybierz() {
      /*
       * Obsługuje proces wyboru sprawy do wyświetlenia szczegółów.
       * W trybie domyślnym lista spraw zawiera sprawy z roku bieżącego ze wszystkich jrwa.
       * Użytkownik ma możliwość zmiany roku i/lub numerów jrwa, które zmienią listę spraw do wyboru z listy.
       *
       * Obsługuje widok sprawy/wybierz
       *
       * Parametry:
       *  - brak
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      $sprawy = $this->sprawaModel->pobierzNumerySpraw();
      $jrwaLista = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Wybierz sprawę do wyświetlenia',
        'sprawy' => $sprawy,
        'jrwa' => $jrwaLista,
        'znak' => '',
        'znak_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['znak'] = $_POST['znak'];
        $sprawa = $this->sprawaModel->pobierzSprawePoZnaku($data['znak']);

        if ($sprawa) {
          redirect('sprawy/szczegoly/' .$sprawa->id);
        } else {
          $data['znak_err'] = "Podana sprawa nie istnieje!";
          $this->view('sprawy/wybierz', $data);
        }

      } else {

        $this->view('sprawy/wybierz', $data);
      }
    }

    public function dodaj_przychodzace($id, $pismo=0) {
      /*
       * Widok, który umożliwia przypisanie pisma przychodzącego do sprawy.
       * Do sprawy można tylko przypisać pismo przychodzące, które:
       *  a) nie jest przypisane do żadnej sprawy
       *  b) nie jest oznaczone jako ad acta
       *  c) jest zadekretowane na zalogowanego pracownika
       *
       * Parametry:
       *  - id => id sprawy
       *
       * Obsługuje widok: sprawy/dodaj_przychodzace/id
       *
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // czy sprawa nie jest zakończona
      $this->sprawdzCzyZakonczona($id);

      // jeżeli link ma tylko jeden parametr to wyświetl zestawienie
      if ($pismo == 0) {

        $sprawa = $this->sprawaModel->pobierzSprawePoId($id);
        $pisma = $this->przychodzacaModel->pobierzPismaDoZrobienia($_SESSION['user_id']);

        $data = [
          'title' => 'Przypisz pismo przychodzące do sprawy ' . $sprawa->znak,
          'id' => $id,
          'sprawa' => $sprawa,
          'pisma' => $pisma
        ];

        $this->view('sprawy/dodaj_przychodzace', $data);
      } else {
        // nie chcemy dodawać do metryki pisma którego nie możemy
        if ($this->przychodzacaModel->czyMoznaDodacPismoDoSprawy($pismo)) {
          $this->metrykaModel->dodajMetryke($id, 1, $_SESSION['user_id'], 1, $pismo);
          $wiadomosc = "Pismo zostało pomyślnie przypisane do sprawy.";
          flash('sprawy_szczegoly', $wiadomosc);
        } else {
          $wiadomosc = "Pismo nie może zostać przypisane do sprawy.";
          flash('sprawy_szczegoly', $wiadomosc, 'alert alert-danger');
        }
        redirect('sprawy/szczegoly/'.$id);
      }
    }

    public function dodaj_wychodzace($id) {
      /*
       * Obsługuje proces dodawania pisma wychodzącego w ramach sprawy.
       *
       * Proces dodawania pisma wychodzącego jest zbliżony do dodawania korespondencji przychodzącej.
       * Różnicę stanowią daty pisma i wpływu (w tym przypadku wysłania/odbioru).
       * Pismo wychodzące ma zawsze datę pisma równą dacie dodania do bazy danych.
       *
       * Data wysłania dodawana jest później w momencie wysyłki lub odbioru osobistego.
       *
       * Pismo wychodzące może być zwykłym pismem, decyzją lub postanowieniem.
       * Jeżeli pismo jest decyzja lub postanowieniem to należy ustalić kolejny numer decyzji lub postanowienie
       * w ramach danego jrwa i wstawić go do pola oznaczenie (osobna metoda ajax przy zmianie guzika radio).
       * Dana ta jest tylko pomocnicza, gdyż pełny numer ma format rozbudowany i system nie jest w stanie
       * sprawdzić czy oznaczenie decyzji/postanowienia jest poprawne.
       * Nie ma sensu również wprowadzać jakichś wzorców jak w ramach każdego jrwa numery decyzji mają różne formaty.
       *
       * Obsługuje widok: sprawy/dodaj_wychodzace
       *
       * Parametry:
       *  - id => id sprawy, w ramach której rejestrowane jest pismo wychodzące
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // czy sprawa nie jest zakończona
      $this->sprawdzCzyZakonczona($id);

      $sprawa = $this->sprawaModel->pobierzSprawePoId($id);
      $podmioty = $this->podmiotModel->pobierzPodmioty();

      $data = [
        'title' => 'Dodaj pismo wychodzące w ramach sprawy ' . $sprawa->znak,
        'id' => $id,
        'jrwa' => $sprawa->id_jrwa,  //potrzebne przy ustaleniu numeru decyzji/postanowieniu
        'czy_nowy' => 0,
        'podmiot_nazwa' => '',
        'podmiot_adres' => '',
        'podmiot_poczta' => '',
        'dotyczy' => '',
        'czy_dp' => 0,
        'oznaczenie_dp' => '',
        'dotyczy_dp' => '',
        'podmioty' => $podmioty,
        'podmiot_nazwa_err' => '',
        'podmiot_adres_err' => '',
        'podmiot_poczta_err' => '',
        'dotyczy_err' => '',
        'oznaczenie_dp_err' => '',
        'dotyczy_dp_err' => ''
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
        $data['czy_dp'] = $_POST['radioDP'];
        $data['oznaczenie_dp'] = trim($_POST['oznaczenieDP']);
        $data['dotyczy_dp'] = trim($_POST['dotyczyDP']);

        $data['podmiot_nazwa_err'] = $this->validator->sprawdzPodmiot($data['podmiot_nazwa'], 4, $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->validator->sprawdzDlugosc($data['podmiot_adres'], 6, $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->validator->sprawdzDlugosc($data['podmiot_poczta'], 6, $data['czy_nowy']);
        $data['dotyczy_err'] = $this->validator->sprawdzDlugosc($data['dotyczy'], 10);
        $data['oznaczenie_dp_err'] = $this->validator->sprawdzDlugosc($data['oznaczenie_dp'], 1, $data['czy_dp']);
        $data['dotyczy_dp_err'] = $this->validator->sprawdzDlugosc($data['dotyczy_dp'], 10, $data['czy_dp']);

        if (empty($data['podmiot_nazwa_err']) &&
            empty($data['podmiot_adres_err']) &&
            empty($data['podmiot_poczta_err']) &&
            empty($data['dotyczy_err']) &&
            empty($data['oznaczenie_dp_err']) &&
            empty($data['dotyczy_dp_err'])) {

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

          $id_pisma = $this->wychodzacaModel->dodajWychodzaca($data);
          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($id, 2, $_SESSION['user_id'], 2, $id_pisma);

          if ($data['czy_dp'] == '1') {
            // dodaj decyzję
            $decyzja = $this->decyzjaModel->dodajDecyzje($id_pisma, $data['oznaczenie_dp'], $data['dotyczy_dp'], $sprawa->id_jrwa);
          } elseif ($data['czy_dp'] == '2') {
            // dodaj postanowienie
            $postanowienie = $this->postanowienieModel->dodajPostanowienie($id_pisma, $data['oznaczenie_dp'], $data['dotyczy_dp'], $sprawa->id_jrwa);
          }

          $wiadomosc = "Pismo wychodzące zostało dodane pomyślnie.";
          if ($decyzja) {
            $wiadomosc .= " Decyzja: $decyzja->numer";
          }
          if ($postanowienie) {
            $wiadomosc .= " Postanowienie: $postanowienie->numer";
          }
          flash('sprawy_szczegoly', $wiadomosc);
          redirect('sprawy/szczegoly/'.$sprawa->id);
        }
      }

      $this->view('sprawy/dodaj_wychodzace', $data);
    }

    public function dodaj_inny($id) {
      /*
       * Obsługuje proces dodawania innego dokumentu w ramach sprawy.
       *
       * Proces dodawania innego dokumentu jest zbliżony do dodawania korespondencji przychodzącej.
       * Inny dokument to dokument przypisywany tylko do akt sprawy, na przykład:
       *  - notatka służbowa
       *  - zwrotka
       *  - protokół
       * Nie ma totaj zamkniętego katalogu więc użytkownik ma dużą swobodę co do zawartości pól.
       * Sprawdzanie tylko dotyczy ilości wpisanych znaków.
       *
       * Obsługuje widok: sprawy/dodaj_inny
       *
       * Parametry:
       *  - id => id sprawy, w ramach której dodawany jest inny dokument
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      // czy sprawa nie jest zakończona
      $this->sprawdzCzyZakonczona($id);

      $sprawa = $this->sprawaModel->pobierzSprawePoId($id);

      $data = [
        'title' => 'Dodaj inny dokument w ramach sprawy ' . $sprawa->znak,
        'id' => $id,
        'rodzaj' => '',
        'dotyczy' => '',
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

          $id_pisma = $this->innyModel->dodajInnyDokument($data);
          // dodaj wpis do metryki
          $this->metrykaModel->dodajMetryke($id, 3, $_SESSION['user_id'], 3, $id_pisma);

          $wiadomosc = "Dodano pomyślnie dokument: " . $data['rodzaj'];
          flash('sprawy_szczegoly', $wiadomosc);
          redirect('sprawy/szczegoly/'.$sprawa->id);
        }
      }

      $this->view('sprawy/dodaj_inny', $data);
    }


    public function ajax_lista($rok, $numer=-1) {
      /*
       * Pobiera listę numerów spraw w podanym roku i/lub numerze jrwa drukuje je w postaci json.
       * Zastosowanie do zapytania ajax.
       * Jeżeli numery nie istnieją lub wystąpił błąd znak ma wartość -1
       * Wartość numeru -1 oznacza, że nie wybrano jrwa.
       *
       * Funkcja nie obsługuje widoku.
       *
       * Parametry:
       *  - rok => rok, w którym sprawa została założona
       *  - numer => numer (nie id) jrwa, którego sprawa dotyczy
       * Zwraca:
       *  - echo json postaci: [{znak: }]
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

      if ($numer != -1) {
        $jrwa = $this->jrwaModel->pobierzJrwaPoNumerze($numer);
        if ($jrwa) {
          $this->pokazListeNumerowSpraw($rok, $jrwa->id);
        } else {
          echo '[{"znak":"-1"}]';
        }
      } else {
        $this->pokazListeNumerowSpraw($rok, -1);
      }
    }

    private function pokazListeNumerowSpraw($rok, $jrwa) {
      /*
       * Pomocnicza funkcja, która na podstawie roku i id jrwa wyświetla dane w postaci json.
       *
       * Parametry:
       *  - rok => rok założenia sprawy
       *  - jrwa => id numeru jrwa
       * Zwraca:
       *  - echo json postaci: [{znak: }]
       */

      $numery = $this->sprawaModel->pobierzNumerySpraw($rok, $jrwa);
      echo json_encode($numery);
    }


    private function utworzZnakSprawy($jrwa) {
      /*
       * Funkcja, która tworzy znak sprawy na podstawie podanego numeru jrwa.
       * Format znaku sprawy
       * PRZEDROSTEK.nrJrwa.nrSprawy.ROK.PRZYROSTEK
       *
       * PRZEDROSTEK - określony jest w ogólnych ustawieniach systemu
       * nrJrwa - numer JRWA dla którego sprawa jest zarejestrowana
       * nrSprawy - kolejny numer sprawy w danym roku w danym jrwa - liczba dotychczasowych spraw +1
       * ROK - czterocyfrowy rok rejestracji sprawy
       * PRZYROSTEK - określony w ustawieniach ogólnych lub użytkownika
       *
       * Parametry:
       *  - jrwa => id numer jrwa
       * Zwraca:
       *  - string
       */

      $separator = '.';
      $rok = date('Y');
      $nrSprawy = $this->sprawaModel->pobierzLiczbeSpraw($jrwa, $rok) + 1;
      $przedrostek = 'PZD';
      $przyrostek = '';
      $obiektJrwa = $this->jrwaModel->pobierzJrwaPoId($jrwa);
      $nrJrwa = $obiektJrwa->numer;

      $znak = $przedrostek.$separator.$nrJrwa.$separator.$nrSprawy.$separator.$rok.$separator.$przyrostek;

      // skróć znak jeżeli nie ma przedrostka
      $znak = substr($znak, 0, -1);

      return $znak;
    }

    private function sprawdzCzyZakonczona($id) {
      /*
       * Funkcja, która kontroluje czy sprawa jest zakończona.
       * Do zakończonej sprawy nie można dodać nowych dokumentów czy też ponowniej jej zakończyć.
       *
       * Jeżeli sprawa jest zakończona przekierowuje na stronę szczegółów
       * z komunikatem że sprawa jest zakończona.
       *
       * Parametry:
       *  - id => id sprawy
       */

     if ($this->sprawaModel->czyZakonczona($id)) {
          $wiadomosc = "Operacja nie jest możliwa! Sprawa jest zakończona.";
          flash('sprawy_szczegoly', $wiadomosc, 'alert alert-danger');
          redirect('sprawy/szczegoly/'.$id);
     }

    }

    private function tworzHtmlPrzychodzace($dokument) {
      /*
       * Funkcja pomocnicza, która tworzy html dla danych pisma przychodzącego
       *
       * Parametry:
       *  - dokument => obiekt pisma przychodzącego
       * Zwraca:
       *  - string - html z danymi pisma przychodzącego
       */

      $html = '<p class="col-sm col-12"><span class="badge badge-dark p-2">nr rejestru:</span> ' . $dokument->nr_rejestru . '</p>';
      $html.= '<p class="col-sm-4 col-12"><span class="badge badge-dark p-2">znak:</span> ' . $dokument->znak . '</p>';
      $html.= '<p class="col-sm-5 col-12"><span class="badge badge-dark p-2">nadawca:</span> ' . $dokument->nazwa . '</p>';
      $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">dotyczy:</span> ' . $dokument->dotyczy . '</p>';
      $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">data pisma:</span> ' . $dokument->data_pisma . '</p>';
      $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">data wpływu:</span> ' . $dokument->data_wplywu . '</p>';
      $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">liczba załączników:</span> ' . $dokument->liczba_zalacznikow . '</p>';
      return $html;
    }

    private function tworzHtmlWychodzace($dokument) {
      /*
       * Funkcja pomocnicza, która tworzy html dla danych pisma wychodzącego
       *
       * Parametry:
       *  - dokument => obiekt pisma wychodzącego
       * Zwraca:
       *  - string - html z danymi pisma wychodzącego
       */

      $html = '<p class="col-sm col-12"><span class="badge badge-dark p-2">data pisma:</span> ' . substr($dokument->utworzone, 0, 10) . '</p>';
      $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">odbiorca:</span> ' . $dokument->nazwa . '</p>';
      $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">dotyczy:</span> ' . $dokument->dotyczy . '</p>';
      if ($dokument->decyzjaId) {
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">numer decyzji:</span> <a href="' . URLROOT . '/decyzje/edytuj/' . $dokument->decyzjaId . '" title="Zmień dane decyzji">' . $dokument->decyzjaNumer . '</a></p>';
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">treść decyzji:</span> ' . $dokument->decyzjaDotyczy . '</p>';
      }
      if ($dokument->postanowienieId) {
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">numer postanowienia:</span> <a href="' . URLROOT . '/postanowienia/edytuj/' . $dokument->postanowienieId . '" title="Zmień dane postanowienia">' . $dokument->postanowienieNumer . '</a></p>';
        $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">treść postanowienia:</span> ' . $dokument->postanowienieDotyczy . '</p>';
      }
      return $html;
    }

    private function tworzHtmlInnyDokument($dokument) {
      /*
       * Funkcja pomocnicza, która tworzy html dla danych innego dokumentu
       *
       * Parametry:
       *  - dokument => obiekt innego dokumentu
       * Zwraca:
       *  - string - html z danymi innego dokumentu
       */

      $html = '<p class="col-sm col-12"><span class="badge badge-dark p-2">data dokumentu:</span> ' . substr($dokument->utworzone, 0, 10) . '</p>';
      $html.= '<p class="col-sm col-12"><span class="badge badge-dark p-2">rodzaj:</span> ' . $dokument->rodzaj . '</p>';
      $html.= '<p class="col-12 py-sm-3"><span class="badge badge-dark p-2">dotyczy:</span> ' . $dokument->dotyczy . '</p>';
      return $html;
    }

    private function tworzHtmlDokumentu($rodzaj, $dokument) {
      /*
       * Funkcja pomocnicza, która tworzy html dla danych dokumentu
       * Zawiera wspólne elementy dla wszystkich rodzajów dokumentu.
       * Elementy szczegółowe w zależności od rodzaju są pobierane z pomocniczych funkcji.
       *
       * Parametry:
       *  - rodzaj => rodzaj dokumentu
       *  - dokument => obiekt danego dokumentu
       * Zwraca:
       *  - string - html z danymi danego dokumentu
       */

      $html = '<div class="card border-secondary">
               <div class="card-header">
                 <p class="mr-auto d-inline">Szczegóły wybranego dokumentu: </p>';
      if ($rodzaj == '2') {
        $html.= '<a href="' . URLROOT . '/wychodzace/edytuj/' . $dokument->id . '" class="btn btn-success float-right">Edytuj dokument</a>';
      }
      if ($rodzaj == '3') {
        $html.= '<a href="' . URLROOT . '/inne/edytuj/' . $dokument->id . '" class="btn btn-success float-right">Edytuj dokument</a>';
      }
      $html.= '</div>
               <div class="card-body">
               <div class="row">';
      switch ($rodzaj) {
        case '1':
          $html.= $this->tworzHtmlPrzychodzace($dokument);
          break;
        case '2':
          $html.= $this->tworzHtmlWychodzace($dokument);
          break;
        case '3':
          $html.= $this->tworzHtmlInnyDokument($dokument);
          break;
      }
      $html.='</div></div></div>';
      return $html;
    }


  }
