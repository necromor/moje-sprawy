<?php
  /*
   *  Kontroler Pracownicy odpowiedzialny jest za obsługę modelu Pracownik z widokami.
   *  Nazwy metod jak w każdym kontrolerze odpowiadają częściom adresu url (co wynika
   *  ze specifiki TraversyMVC)
   *
   */

  class Pracownicy extends Controller {

    // zmienna określająca poziomy dostępu pracownika
    // sekretariat - dostęp do wszystkich opcji za wyjątkiem tych zarezerwoanych dla admina
    // księgowość - brak dostępu do rejestracji i edycji przychodzących
    // zwykły - brak dostępu do rejestracji i edycji przychodzących oraz zestawienia faktur
    private $POZIOMY = [
      0 => 'sekretariat',
      1 => 'księgowość',
      2 => 'zwykły'
    ];

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z modelami 
       */

      $this->pracownikModel = $this->model('Pracownik');
    }


    public function zestawienie() {
      /*
       * Tworzy zestaw obiektów Pracownik z podmianą aktywności oraz poziomu na wiadomość tekstową
       *
       * Obsługuje widok: pracownicy/zestawienie
       */

      $pracownicy = $this->pracownikModel->pobierzWszystkichPracownikow();

      // podmiana poziomów i aktywności na tekst
      foreach ($pracownicy as $pracownik) {
        $pracownik->poziom = $this->POZIOMY[$pracownik->poziom];
        if ($pracownik->aktywny == 0) {
          $pracownik->aktywny = 'Nie';
        } else {
          $pracownik->aktywny = 'Tak';
        }
      }

      $data = [
        'title' => 'Zestawienie pracowników',
        'pracownicy' => $pracownicy
      ];

      $this->view('pracownicy/zestawienie', $data);
    }

    public function dodaj() {
      /* 
       * Obsługuje proces dodawania nowego pracownika.
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
       * Obsługuje widok: pracownicy/dodaj
       */

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Dodaj pracownika',
          'poziomy' => $this->POZIOMY,
          'imie' => trim($_POST['imie']),
          'nazwisko' => trim($_POST['nazwisko']),
          'login' => trim($_POST['login']),
          'poziom' => $_POST['poziom'],
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $data['imie_err'] = $this->sprawdzImie($data['imie']);
        $data['nazwisko_err'] = $this->sprawdzNazwisko($data['nazwisko']);
        $data['login_err'] = $this->sprawdzLogin($data['login']);

        if (empty($data['imie_err']) && empty($data['nazwisko_err']) && empty($data['login_err'])) {
 
          //dodaj podstawowe hasło pracownika jako zakodowany login
          $data['haslo'] = password_hash($data['login'], PASSWORD_DEFAULT);

          $this->pracownikModel->dodajPracownika($data);

          // twórz wiadomość do wyświetlenia po przekierowaniu
          $wiadomosc = "Pracownik <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> został dodany pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 

        } else {
          // brudny

          $this->view('pracownicy/dodaj', $data);
        }

      } else {
        // czysty

        $data = [
          'title' => 'Dodaj pracownika',
          'poziomy' => $this->POZIOMY,
          'imie' => '',
          'nazwisko' => '',
          'login' => '',
          'poziom' => 2,
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $this->view('pracownicy/dodaj', $data);
      }
    }

    public function edytuj($id) {
      /* 
       * Obsługuje proces edycji istniejącego pracownika.
       * Sposób działania jest identyczy jak funkcji dodaj() z niewielką różnicą
       * w trybie czystym - do pól formularza wprowadzane są dane edytowanego pracownika.
       *
       * Obsługuje widok: pracownicy/edytuj/id
       *
       * Parametry:
       *  - id => id edytowanego pracownika
       */

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Zmień dane pracownika',
          'id' => $_POST['id'],
          'poziomy' => $this->POZIOMY,
          'imie' => trim($_POST['imie']),
          'nazwisko' => trim($_POST['nazwisko']),
          'login' => trim($_POST['login']),
          'poziom' => $_POST['poziom'],
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $data['imie_err'] = $this->sprawdzImie($data['imie']);
        $data['nazwisko_err'] = $this->sprawdzNazwisko($data['nazwisko']);
        $data['login_err'] = $this->sprawdzLogin($data['login'], $id);

        if (empty($data['imie_err']) && empty($data['nazwisko_err']) && empty($data['login_err'])) {

          $this->pracownikModel->edytujPracownika($data);

          $wiadomosc = "Dane pracownika <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> zostały zmienione pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 

        } else {
          // brudny

          $this->view('pracownicy/edytuj', $data);
        }

      } else {
        // czysty

        $pracownik = $this->pracownikModel->pobierzPracownikaPoId($id);

        $data = [
          'title' => 'Zmień dane pracownika',
          'id' => $id,
          'poziomy' => $this->POZIOMY,
          'imie' => $pracownik->imie,
          'nazwisko' => $pracownik->nazwisko,
          'login' => $pracownik->login,
          'poziom' => $pracownik->poziom,
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $this->view('pracownicy/edytuj', $data);
      }
    }

    public function zmien_haslo($id) {
      /* 
       * Obsługuje proces zmiany hasła pracownika.
       * Sposób działania jest identyczy jak funkcji dodaj().
       *
       * Proces zmiany hasła odbywa się w następujący sposób:
       * 1) sprawdzenie czy hasło użytkownika jest zgodne z tym podanym
       * 2) sprawdzenie czy podane nowe hasła są zgodne
       * 3) zmiana hasła
       * 4) przekierowanie na stronę główną
       *
       * Obsługuje widok: pracownicy/zmien_haslo
       */

      ///////////////////////////////
      // TYLKO ZALOGOWANY MOŻE ZMIEŃIĆ SWOJE HASŁO
      // USTAW SPRAWDZANIE PO STWORZENIU LOGOWANIA
      // ID Z SESJI NIE Z ADRESU
      //////////////////////////////

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Zmień hasło',
          'id' => $id,
          'hasloS' => trim($_POST['hasloS']),
          'hasloN1' => trim($_POST['hasloN1']),
          'hasloN2' => trim($_POST['hasloN2']),
          'hasloS_err' => '',
          'hasloN1_err' => '',
          'hasloN2_err' => '',
        ];

        $data['hasloS_err'] = $this->sprawdzStareHaslo($data['hasloS'], $id);
        $data['hasloN1_err'] = $this->sprawdzNoweHaslo($data['hasloN1']);
        if ($data['hasloN2'] != $data['hasloN1']) {
          $data['hasloN2_err'] = 'Podane hasła się różnią';
        }

        if (empty($data['hasloS_err']) && empty($data['hasloN1_err']) && empty($data['hasloN2_err'])) {

          $haslo = password_hash($data['hasloN1'], PASSWORD_DEFAULT);
          $this->pracownikModel->zmienHaslo($data['id'], $haslo);

          // tymczasowo przekierowanie na zestawienie
          // docelowo ma być strona główna użytkownika
          $wiadomosc = "Hasło zmienione pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 

        } else {
          // brudny

          $this->view('pracownicy/zmien_haslo', $data);
        }

      } else {
        // czysty

        $data = [
          'title' => 'Zmień hasło',
          'id' => $id,
          'hasloS' => '',
          'hasloN1' => '',
          'hasloN2' => '',
          'hasloS_err' => '',
          'hasloN1_err' => '',
          'hasloN2_err' => ''
        ];

        $this->view('pracownicy/zmien_haslo', $data);
      }
    }


   public function aktywuj($id) {
      /* 
       * Obsługuje proces zmiany statusu pracownika.
       * Po sprawdzeniu czy pracownik jest aktywny ustawiany jest przeciwny status
       * i wywoływana metoda zmiany statusu.
       *
       * Funkcja nie obsługuje widoku.
       * Funkcja dostępna jedynie dla admina.
       *
       * Parametry:
       *  - id => id pracownika
       */

     $status = 1;
     $tekst = "aktywny";

     if ($this->pracownikModel->czyAktywny($id)) {
       $status = 0;
       $tekst = "nieaktywny";
     } 

     $this->pracownikModel->zmienStatus($id, $status);
     $wiadomosc = "Status pracownika został zmieniony pomyślnie na <strong>$tekst</strong>.";
     flash('pracownicy_wiadomosc', $wiadomosc);
     redirect('pracownicy/zestawienie'); 
   }

   public function resetuj($id) {
      /* 
       * Obsługuje proces resetu hasła pracownika.
       * Resetowanie hasła polega na ustawieniu hasła o wartości równej loginowi pracownika
       * podobnie jak przy dodawaniu.
       * Proces jest nieodwracalny i wymusza ustawienie nowego hasła przez pracownika.
       *
       * Funkcja nie obsługuje widoku.
       * Funkcja dostępna jedynie dla admina.
       *
       * Parametry:
       *  - id => id pracownika
       */

     $pracownik = $this->pracownikModel->pobierzPracownikaPoId($id);
     $haslo = password_hash($pracownik->login, PASSWORD_DEFAULT);

     $this->pracownikModel->zmienHaslo($id, $haslo);
     $wiadomosc = "Hasło pracownika zostało ustawione na domyślne.";
     flash('pracownicy_wiadomosc', $wiadomosc);
     redirect('pracownicy/zestawienie'); 
   }




   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function sprawdzImie($tekst) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego imienia do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - imię musi mieć przynajmniej 2 znaki
      *
      *  Parametry:
      *   - tekst => wprowadzone imię
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć imię.";
     } elseif (strlen($tekst) < 2) {
       $error = "Imię musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzNazwisko($tekst) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego nazwiska do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - nazwisko musi mieć przynajmniej 2 znaki
      *
      *  Parametry:
      *   - tekst => wprowadzone nazwisko
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć nazwisko.";
     } elseif (strlen($tekst) < 2) {
       $error = "Nazwisko musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzLogin($tekst, $id=0) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego loginu do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - login musi mieć przynajmniej 2 znaki
      *  - login musi być unikatowy
      *
      *  Parametry:
      *   - tekst => wprowadzony login
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć login.";
     } elseif (strlen($tekst) < 2) {
       $error = "Login musi mieć przynajmniej 2 znaki.";
     } elseif ($this->pracownikModel->czyIstniejeLogin($tekst, $id)) {
       $error = "Podany login jest już zajęty.";
     }

     return $error;
   }


   private function sprawdzStareHaslo($haslo, $id) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego hasła do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - hasło musi być zgodne z tym w bazie danych
      *
      *  Parametry:
      *   - tekst => wprowadzone hasło
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($haslo == '') {
       $error = "Musisz podać stare hasło.";
     } elseif (!$this->pracownikModel->sprawdzHaslo($haslo, $id)) {
       $error = "Podano nieporawne hasło.";
     }

     return $error;
   }

   private function sprawdzNoweHaslo($haslo) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego nowego hasła do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - hasło musi mieć przynajmniej 6 znaków
      *
      *  Parametry:
      *   - tekst => wprowadzone hasło
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($haslo == '') {
       $error = "Musisz podać nowe hasło.";
     } elseif (strlen($haslo) < 6) {
       $error = "Hasło musi mieć przynajmniej 6 znaków.";
     }

     return $error;
   }


  }
