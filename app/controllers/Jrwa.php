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
      czyZalogowany();
      czyZalogowanyAdmin();

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
      czyZalogowany();
      czyZalogowanyAdmin();

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


        }



      } else {

        // czysty
        $this->view('jrwa/dodaj', $data);
      }
    }



   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function sprawdzNumer($tekst) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego numeru jrwa do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - numer musi mieć od 1 do 4 cyfr - pole formularza ma ustawione
      *  takie filtrowanie więc tutaj niepotrzebne jest sprawdzanie tego warunku
      *  - pole nie jest sprawdzanie jeżeli dodawana jest grupa numerów
      *
      *  Parametry:
      *   - tekst => wprowadzony numer
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
      */

     $error = '';

     if ($tekst == '') {
       $error = "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
     }

     return $error;
   }

   private function sprawdzOpis($tekst) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego opisu numeru jrwa do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - opis musi mieć przynajmniej 10 znaków
      *
      *  Parametry:
      *   - tekst => wprowadzony opis
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł
      */

     $error = '';

     if ($tekst == '') {
       $error = "Musisz podać numer Jednolitego Rzeczowego Wykazu Akt.";
     } elseif (strlen($tekst) < 10 ) {
       $error = "Opis musi mieć przynajmniej 10 znaków.";
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

