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
            $przych = $this->przychodzacaModel->pobierzPrzychodzacaPoId($m->id_dokument);
            $m->dokument = 'p' . time($przych->utworzone);
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

        $data['jrwa_err'] = $this->sprawdzJrwa($data['jrwa']);
        $data['temat_err'] = $this->sprawdzTemat($data['temat']);

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

        $data['temat_err'] = $this->sprawdzTemat($data['temat']);

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

      // nie wznawiaj sprawy nie zakończonej sprawy
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


    /*
     * FUNKCJE SPRAWDZAJĄCE
     */

    private function sprawdzJrwa($tekst) {
      /*
       * Funkcja pomocnicza - sprawdza poprawność wprowadzonego numeru jrwa do formularza.
       * Zasady:
       *  - pole nie może być puste
       *  - numer musi istnieć w bazie danych
       *
       *  Parametry:
       *   - tekst => wprowadzony numer
       *  Zwraca:
       *   - sting zawierający komunikat błędu jeżeli taki wystąpł
       */

      $error = '';

      if ($tekst == '') {
        $error = "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
      } elseif (!$this->jrwaModel->czyIstniejeJrwa($tekst, 0)) {
        $error = "Podany numer JRWA nie istnieje.";
      }

      return $error;
    }

    private function sprawdzTemat($tekst) {
      /*
       * Funkcja pomocnicza - sprawdza poprawność wprowadzonego tematu sprawy do formularza.
       * Zasady:
       *  - pole nie może być puste
       *  - temat musi mieć przynajmniej 10 znaków
       *
       *  Parametry:
       *   - tekst => wprowadzony temat
       *  Zwraca:
       *   - sting zawierający komunikat błędu jeżeli taki wystąpł
       */

      $error = '';

      if ($tekst == '') {
        $error = "Musisz podać temat sprawy.";
      } elseif (strlen($tekst) < 10 ) {
        $error = "Temat sprawy nie może mieć mniej niż 10 znaków.";
      }

      return $error;
    }

  }
