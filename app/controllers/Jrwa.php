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

      //$this->podmiotModel = $this->model('Podmiot');
      //$this->pracownikModel = $this->model('Pracownik');
      $this->jrwaModel = $this->model('JrwaM');
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
       * Sprawdzanie poprawności wprowadzonych danych polega jedynie
       * na sprawdzeniu czy pola nie są puste.
       *
       * Oprócz standardowego dodawanie jednego numeru jrwa widok
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
          $data['numer_err'] = $this->sprawdzNumer($data['numer']);
          $data['opis_err'] = $this->sprawdzOpis($data['opis']);

          if (empty($data['numer_err']) && empty($data['opis_err'])) {
            $numer = $data['numer'];
            $opis = $data['opis'];
            $this->jrwaModel->dodajJrwa($numer, $opis);

            $wiadomosc = "Numer <strong>$numer</strong> [<em>$opis</em>] został dodany poprawnie.";
            flash('jrwa_wiadomosc', $wiadomosc);
            redirect('jrwa/zestawienie');
          } else {
            // brudny
            $this->view('jrwa/dodaj', $data);
          }
        } else {
          // grupa
          $data['grupa_err'] = $this->przetworzGrupeJrwa($data['grupa']);

          if (empty($data['grupa_err'])) {
            $wiadomosc = "Numer jrwa zostały dodane poprawnie.";
            flash('jrwa_wiadomosc', $wiadomosc);
            redirect('jrwa/zestawienie');
          } else {
            // brudny
            $this->view('jrwa/dodaj', $data);
          }
        }

      } else {

        // czysty
        $this->view('jrwa/dodaj', $data);
      }
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

      $data = [
        'title' => 'Zmień dane pozycji JRWA',
        'id' => $id,
        'numer' => '',
        'opis' => '',
        'numer_err' => '',
        'opis_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['numer'] = trim($_POST['numer']);
        $data['opis'] = trim($_POST['opis']);

        $data['numer_err'] = $this->sprawdzNumer($data['numer'], $id);
        $data['opis_err'] = $this->sprawdzOpis($data['opis']);

        if (empty($data['numer_err']) && empty($data['opis_err'])) {
          $numer = $data['numer'];
          $opis = $data['opis'];
          $this->jrwaModel->edytujJrwa($numer, $opis, $id);

          $wiadomosc = "Numer <strong>$numer</strong> [<em>$opis</em>] został zmieniony.";
          flash('jrwa_wiadomosc', $wiadomosc);
          redirect('jrwa/zestawienie');
        } else {
          // brudny
          $this->view('jrwa/edytuj', $data);
        }

      } else {
        // czysty

        $jrwa = $this->jrwaModel->pobierzJrwaPoId($id);
        $data['numer'] = $jrwa->numer;
        $data['opis'] = $jrwa->opis;

        $this->view('jrwa/edytuj', $data);
      }
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

      $error = '';

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
        $elementy[0] = trim($elementy[0]);
        $elementy[1] = trim($elementy[1]);
        // czy linia jest postaci numer:opis
        if (count($elementy) != 2) {
          return 'W zbiorze danych wystąpił nieprawidłowy format linii.';
        }

        // sprawdzenie poszczególnych numerów i opisów
        $err_numer = $this->sprawdzNumer($elementy[0]);
        $err_opis = $this->sprawdzOpis($elementy[1]);
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


   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function sprawdzNumer($tekst, $id=0) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego numeru jrwa do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - numer musi mieć od 1 do 4 cyfr
      *  - numer nie może istnieć w bazie danych
      * Przy edycji numeru ostatni warunek musi uwzględniać fakt, że użytkownik może nie chcieć zmienić
      * istniejącego numeru.
      *
      *  Parametry:
      *   - tekst => wprowadzony numer
      *   - id => id edytowanego numeru
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
      */

     $error = '';

     if ($tekst == '') {
       $error = "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
     } elseif (!preg_match('/^[0-9]{1,4}$/', $tekst)) {
       $error = "Format numeru jrwa to od 1 do 4 cyfr.";
     } elseif ($this->jrwaModel->czyIstniejeJrwa($tekst, $id)) {
       $error = "W bazie danych istnieje już JRWA o numerze $tekst.";
     }

     return $error;
   }

   private function sprawdzOpis($tekst) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego opisu numeru jrwa do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - opis musi mieć przynajmniej X znaków
      *
      *  Parametry:
      *   - tekst => wprowadzony opis
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
      */

     $limit = 8; //minimalna liczba znaków opisu
     $error = '';

     if ($tekst == '') {
       $error = "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
     } elseif (strlen($tekst) < $limit ) {
       $error = "Opis musi mieć przynajmniej $limit znaków. [$tekst]";
     }

     return $error;
   }

   private function sprawdzGrupe($grupa) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonej grup numerów i opisów jrwa.
      * Zasady:
      *  - numer i opis muszą spełniać warunki z funkcji powyżej
      *
      *  Parametry:
      *   - grupa => dane opisów numerów i grupy w formie tablicy
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
      */

     $error = '';

     //tymczasowo
     return $error;
   }


  }

