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

    public function zaloguj() {
      /*
       * Obsługuje proces logowania się pracownika.
       * Sposób działania jest identyczy jak funkcji dodaj().
       *
       * Logowanie odbywa się w następujący sposób:
       * 1) sprawdzenie czy w bazie istnieje podany login
       * 2) sprawdzenie czy podane hasło jest zgodne z tym w bazie danych
       *    dla danego loginu
       * 3) przekierowanie na stronę główną
       *
       * Obsługuje widok: pracownicy/zaloguj
       */

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Zaloguj się',
          'login' => trim($_POST['login']),
          'haslo' => trim($_POST['haslo']),
          'imie_err' => '',
          'login_err' => '',
          'haslo_err' => ''
        ];

        $id = $this->pracownikModel->pobierzIdPracownikaPoLoginie($data['login']);
        $data['login_err'] = $this->sprawdzLoginLogowanie($data['login'], $id);

        if(empty($data['login_err'])) {
          $data['haslo_err'] = $this->sprawdzHaslo($data['haslo'], $id);

          if(empty($data['haslo_err'])) {
            // zalogowano pomyślnie więc ustaw id
            // konieczne bo zmiana hasła z niej korzysta
            $_SESSION['user_id'] = $id;

            if($this->czyWymaganaZmianaHasla($id)) {
              $wiadomosc = 'Zalogowano pomyślnie, ale konieczna jest zmiana hasła.';
              flash('pracownicy_zmiana_hasla', $wiadomosc);
              redirect('pracownicy/zmien_haslo'); 
            } else {
              $this->zalogujPracownika($id);
            }
          }
        }

        // brudny
        $this->view('pracownicy/zaloguj', $data);

      } else {
        // czysty

        $data = [
          'title' => 'Zaloguj się',
          'login' => '',
          'haslo' => '',
          'login_err' => '',
          'haslo_err' => ''
        ];

        $this->view('pracownicy/zaloguj', $data);
      }
    }

    public function wyloguj() {
      /*
       * Obsługuje proces wylogowywania pracownika.
       * Usuwa zmienne sesji i kończy sesję.
       *
       * Nie obsługuje widoku.
       */

      unset($_SESSION['user_id']);
      unset($_SESSION['imie_nazwisko']);
      session_destroy();

      redirect('pracownicy/zaloguj');
    }


    public function zmien_haslo() {
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

      $id = $_SESSION['user_id'];

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

        $data['hasloS_err'] = $this->sprawdzHaslo($data['hasloS'], $id);
        $data['hasloN1_err'] = $this->sprawdzNoweHaslo($data['hasloN1'], $data['hasloS']);
        if ($data['hasloN2'] != $data['hasloN1']) {
          $data['hasloN2_err'] = 'Podane hasła się różnią';
        }

        if (empty($data['hasloS_err']) && empty($data['hasloN1_err']) && empty($data['hasloN2_err'])) {

          $haslo = password_hash($data['hasloN1'], PASSWORD_DEFAULT);
          $this->pracownikModel->zmienHaslo($id, $haslo);

          $this->zalogujPracownika($id);

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

   private function zalogujPracownika($id) {
     /*
      * Funkcja pomocnicza - ustawia dane sesji.
      * $_SESSION['id'] jest ustawione wcześniej.
      *
      * Parametry:
      *  - id => id pracownika do zalogowania
      * Zwraca:
      *  - brak
      */

      $_SESSION['imie_nazwisko'] = $this->pracownikModel->pobierzImieNazwisko($id);
      redirect('pages'); 
   }

   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function czyWymaganaZmianaHasla($id) {
     /*
      * Funkcja pomocnicza - sprawdza czy wymagana jest zmiana hasła.
      * Zmiana wymagana jest w dwóch przypadkach:
      * 1) hasło jest domyślne czyli login
      * 2) przekroczona została data zmiany hasła o sprecyzowaną liczbę dni
      *
      * Parametry:
      *  - id => id pracownika
      * Zwraca:
      *  - boolean - true jeżeli wymagana jest zmiana
      */

     $pracownik = $this->pracownikModel->pobierzPracownikaPoId($id);

     if (password_verify($pracownik->login, $pracownik->haslo)){
       return true;
     }

     return $this->czyUplynelaWaznoscHasla($pracownik->zmiana_hasla);
   }

   private function czyUplynelaWaznoscHasla($zmiana) {
     /*
      * Funkcja pomocnicza - sprawdza czy dla podanej dany upłynęło już X dni.
      * Liczba dni ważności hasła pobierana jest z bazy danych.
      *
      * Parametry:
      *  - zmiana => data ostatniej zmiany hasła
      * Zwraca:
      *  - boolean => true jeżeli liczba dni przekroczyła limit
      */

      // tymczasowo - docelowo z bazy danych
      $limit = 60;
      //$teraz = time();
      //$oz = strtotime($zmiana);
      //$roznica = $teraz - $oz;

      //return (round($roznica / (60*60*24)) > $limit);
      //
      // php 5.3 +
      $teraz = new DateTime();
      $oz = new DateTime($zmiana);
      $roznica = $oz->diff($teraz);

      return ($roznica->d > $limit);
   }

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

   private function sprawdzLoginLogowanie($login, $id) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego loginu do formularza logowania.
      * Zasady:
      *  - pole nie może być puste
      *  - login musi istnieć w bazie danych
      *
      *  Parametry:
      *   - login => wprowadzony login
      *  Zwraca:
      *   - sting zawierający komunikat błędu jeżeli taki wystąpł 
      */

     $error = '';

     if ($login == '') {
       $error = "Proszę podać login.";
     } elseif ($id == -1) {
       $error = "Podany login jest nieprawidłowy.";
     }

     return $error;
   }

   private function sprawdzHaslo($haslo, $id) {
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
       $error = "Musisz podać hasło.";
     } elseif (!$this->pracownikModel->sprawdzHaslo($haslo, $id)) {
       $error = "Podano nieporawne hasło.";
     }

     return $error;
   }

   private function sprawdzNoweHaslo($haslo, $stare) {
     /*
      * Funkcja pomocnicza - sprawdza poprawność wprowadzonego nowego hasła do formularza.
      * Zasady:
      *  - pole nie może być puste
      *  - hasło musi mieć przynajmniej 6 znaków
      *  - nowe hasło nie może być takie samo jak stare
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
     } elseif ($haslo == $stare) {
       $error = "Nowe hasło nie może być inne niż obecne.";
     }

     return $error;
   }


  }
