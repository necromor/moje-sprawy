<?php

  /*
   *  Kontroler Jrwa odpowiedzialny jest za obsługę modelu Jrwa z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Jrwa extends Controller {

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami
       */

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

    public function zestawienie() {
      /*
       * Tworzy zestaw obiektów Jrwa
       *
       * Obsługuje widok: jrwa/zestawienie
       */

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

      $jrwa = $this->jrwaModel->pobierzJrwa();

      $data = [
        'title' => 'Zestawienie numerów Jednolitego Rzeczowego Wykazu Akt',
        'jrwa' => $jrwa
      ];

      $this->view('jrwa/zestawienie', $data); 
    }

    public function dodaj() {
      /*
       * Obsługuje proces dodawania nowego numeru jrwa.
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
       * numeru jrwa lub wyświetla brudny formularz.
       *
       * Oprócz standardowego dodawania jednego numeru jrwa widok
       * ten obsługuje również zbiorcze dodawanie numerów, co jest przydatne
       * przy początkowej konfiguracji.
       *
       * Obsługuje widok: jrwa/dodaj
       */

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

      $data = [
        'title' => 'Dodaj numer JRWA',
        'czy_grupa' => '0',
        'numer' => '',
        'opis' => '',
        'grupa' => '',
        'numer_err' => '',
        'opis_err' => '',
        'grupa_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['czy_grupa'] = $_POST['czyGrupa'];
        $data['numer'] = trim($_POST['numer']);
        $data['opis'] = trim($_POST['opis']);
        $data['grupa'] = trim($_POST['grupa']);


        // ścieżka dodawania inna dla grupy inna dla numeru
        if ($data['czy_grupa'] == '0') {
          // pojedynczy numer
          $data['numer_err'] = $this->validator->sprawdzNumerJrwa($data['numer']);
          $data['opis_err'] = $this->validator->sprawdzDlugosc($data['opis'], 8);

          if (empty($data['numer_err']) &&
              empty($data['opis_err'])) {

            $numer = $data['numer'];
            $opis = $data['opis'];
            $this->jrwaModel->dodajJrwa($numer, $opis);

            $wiadomosc = "Numer <strong>$numer</strong> [<em>$opis</em>] został dodany poprawnie.";
            flash('jrwa_wiadomosc', $wiadomosc);
            redirect('jrwa/zestawienie');
          }
        } else {
          // grupa
          $data['grupa_err'] = $this->przetworzGrupeJrwa($data['grupa']);

          if (empty($data['grupa_err'])) {
            $wiadomosc = "Numer jrwa zostały dodane poprawnie.";
            flash('jrwa_wiadomosc', $wiadomosc);
            redirect('jrwa/zestawienie');
          }
        }
      }

      $this->view('jrwa/dodaj', $data);
    }

    public function edytuj($id) {
      /*
       * Obsługuje proces edycji istniejącego numery JRWA.
       * Sposób działania jest identyczy jak funkcji dodaj() z niewielką różnicą
       * w trybie czystym - do pól formularza wprowadzane są dane edytowanego numeru.
       *
       * Obsługuje widok: jrwa/edytuj/id
       *
       * Parametry:
       *  - id => id edytowanego numeru jrwa
       */

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

      $jrwa = $this->jrwaModel->pobierzJrwaPoId($id);

      $data = [
        'title' => 'Zmień dane pozycji JRWA',
        'id' => $id,
        'numer' => $jrwa->numer,
        'opis' => $jrwa->opis,
        'numer_err' => '',
        'opis_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['numer'] = trim($_POST['numer']);
        $data['opis'] = trim($_POST['opis']);

        $data['numer_err'] = $this->validator->sprawdzNumerJrwa($data['numer'], $id);
        $data['opis_err'] = $this->validator->sprawdzDlugosc($data['opis'], 8);

        if (empty($data['numer_err']) &&
            empty($data['opis_err'])) {

          $numer = $data['numer'];
          $opis = $data['opis'];
          $this->jrwaModel->edytujJrwa($numer, $opis, $id);

          $wiadomosc = "Numer <strong>$numer</strong> [<em>$opis</em>] został zmieniony.";
          flash('jrwa_wiadomosc', $wiadomosc);
          redirect('jrwa/zestawienie');
        }
      }

      $this->view('jrwa/edytuj', $data);
    }

    private function przetworzGrupeJrwa($grupa) {
      /*
       * Przetwarza grupę numerów jrwa postaci @numer:opis
       * Sprawdza warunki i jeżeli jakiś nie jest spełniany
       * zwraca komunikat błędu.
       *
       * Jeżeli nie wystąpiły błedy to dodaje numery jrwa.
       * Dodaje dopiero jeżeli wszystkie dane są poprawne.
       *
       * Parametry:
       *  - grupa => dane z formularza - linie postaci @numer:opis
       * Zwraca:
       *  - string => komunikat błędu jeżeli taki wystąpił
       */

      if (empty($grupa)) {
        return 'Nie podano danych.';
      }

      // podział na linie po @
      $linie = explode('@', $grupa);
      //odrzucamy element 0
      array_shift($linie);

      // czy jest choć jedna linia
      if (count($linie) == 0) {
        return 'Każda linia musi być w postaci @numer:opis.';
      }

      $jrwa = [];
      $numery = []; // służy do sprawdzania czy na liście nie ma duplikatów numerów jrwa
      foreach ($linie as $linia) {
        $elementy = explode(":", $linia);
        // czy linia jest postaci numer:opis
        if (count($elementy) != 2) {
          return 'W zbiorze danych wystąpił nieprawidłowy format linii.';
        }

        $elementy[0] = trim($elementy[0]);
        $elementy[1] = trim($elementy[1]);

        // sprawdzenie poszczególnych numerów i opisów
        $err_numer = $this->validator->sprawdzNumerJrwa($elementy[0]);
        $err_opis = $this->validator->sprawdzDlugosc($elementy[1], 8);
        if (!empty($err_numer) || !empty($err_opis)) {
          return "$err_numer <br> $err_opis";
        }

        // czy na liście są powtórzone numery
        if (in_array($elementy[0], $numery, true)) {
          return "W podanej liście występują duplikaty numerów jrwa. [$elementy[0]]";
        }

        // dla danej linii nie było błędów więc dodaj do tablic
        array_push($numery, $elementy[0]);
        array_push($jrwa, array("numer" => $elementy[0], "opis" => $elementy[1]));
      }

      // doszliśmy do tego miejsca i nie ma błędów czyli można dodawać
      foreach ($jrwa as $j) {
        $this->jrwaModel->dodajJrwa($j['numer'], $j['opis']);
      }
      return '';
    }

    public function ajax_jrwa($numer) {
      /*
       * Pobiera dane numeru jrwa i drukuje je w postaci json.
       * Zastosowanie do zapytania ajax.
       * Jeżeli numer nie istnieje w miejsu id wstawiona zostaje wartość -1
       *
       * Funkcja nie obsługuje widoku.
       *
       * Parametry:
       *  - numer => numer pobieranego numeru jrwa
       * Zwraca:
       *  - echo json postaci: { id:, numer:, opis:, utworzone: }
       */

      // tylko zalogowany, ale nie admin
      sprawdzCzyPosiadaDostep(4,0);

       $jrwa = $this->jrwaModel->pobierzJrwaPoNumerze($numer);
       if ($jrwa) {
         echo json_encode($jrwa);
       } else {
         echo '{"id":"-1"}';
       }
    }



  }

