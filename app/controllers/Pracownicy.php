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
      $this->adminModel = $this->model('Admin');

      $this->validator = new Validator();
    }


    public function zestawienie() {
      /*
       * Tworzy zestaw obiektów Pracownik z podmianą aktywności oraz poziomu na wiadomość tekstową
       *
       * Obsługuje widok: pracownicy/zestawienie
       */

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

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

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

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

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['imie'] = trim($_POST['imie']);
        $data['nazwisko'] = trim($_POST['nazwisko']);
        $data['login'] = trim($_POST['login']);
        $data['poziom'] = $_POST['poziom'];

        $data['imie_err'] = $this->validator->sprawdzDlugosc($data['imie'], 2);
        $data['nazwisko_err'] = $this->validator->sprawdzDlugosc($data['nazwisko'], 2);
        $data['login_err'] = $this->validator->sprawdzLogin($data['login']);

        if (empty($data['imie_err']) &&
            empty($data['nazwisko_err']) &&
            empty($data['login_err'])) {

          //dodaj podstawowe hasło pracownika jako zakodowany login
          $data['haslo'] = password_hash($data['login'], PASSWORD_DEFAULT);

          $this->pracownikModel->dodajPracownika($data);

          // twórz wiadomość do wyświetlenia po przekierowaniu
          $wiadomosc = "Pracownik <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> został dodany pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 
        }

      }

      $this->view('pracownicy/dodaj', $data);
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

      // tylko admin
      sprawdzCzyPosiadaDostep(-1,-1);

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

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['imie'] = trim($_POST['imie']);
        $data['nazwisko'] = trim($_POST['nazwisko']);
        $data['login'] = trim($_POST['login']);
        $data['poziom'] = $_POST['poziom'];

        $data['imie_err'] = $this->validator->sprawdzDlugosc($data['imie'], 2);
        $data['nazwisko_err'] = $this->validator->sprawdzDlugosc($data['nazwisko'], 2);
        $data['login_err'] = $this->validator->sprawdzLogin($data['login'], $id);

        if (empty($data['imie_err']) &&
            empty($data['nazwisko_err']) &&
            empty($data['login_err'])) {

          $this->pracownikModel->edytujPracownika($data);

          $wiadomosc = "Dane pracownika <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> zostały zmienione pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 
        }
      }

      $this->view('pracownicy/edytuj', $data);
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
       * 3) sprawdzenie czy upłynął termin ważności hasła
       * 4) przekierowanie na stronę główną
       *
       * Obsługuje widok: pracownicy/zaloguj
       */

      $data = [
        'title' => 'Zaloguj się',
        'login' => '',
        'haslo' => '',
        'login_err' => '',
        'haslo_err' => ''
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['login'] = trim($_POST['login']);
        $data['haslo'] = trim($_POST['haslo']);

        // logowanie admina
        if ($data['login'] == 'admin') {
          $this->logowanieAdmina($data);
        }

        // logowanie zwykłego użytkownika

        $data['login_err'] = $this->validator->sprawdzLoginLogowanie($data['login']);

        if(empty($data['login_err'])) {

          $id = $this->pracownikModel->pobierzIdPracownikaPoLoginie($data['login']);
          $data['haslo_err'] = $this->validator->sprawdzHaslo($data['haslo'], $id);

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
      }

      $this->view('pracownicy/zaloguj', $data);
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
      unset($_SESSION['poziom']);
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

      // musi być zalogowany
      // przekierowanie może nastąpić z logowania z niepełnymi danymi sesji
      if(!isset($_SESSION['user_id'])){
        redirect('pages');
      }

      $id = $_SESSION['user_id'];
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

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['hasloS'] = trim($_POST['hasloS']);
        $data['hasloN1'] = trim($_POST['hasloN1']);
        $data['hasloN2'] = trim($_POST['hasloN2']);

        // admin
        if ($id == '0') {
          $this->zmienHasloAdmina($data);
        } else {
          //pozostali

          $data['hasloS_err'] = $this->validator->sprawdzHaslo($data['hasloS'], $id);
          $data['hasloN1_err'] = $this->validator->sprawdzNoweHaslo($data['hasloN1'], $data['hasloS']);
          if ($data['hasloN2'] != $data['hasloN1']) {
            $data['hasloN2_err'] = 'Podane hasła się różnią';
          }

          if (empty($data['hasloS_err']) &&
              empty($data['hasloN1_err']) &&
              empty($data['hasloN2_err'])) {

            $haslo = password_hash($data['hasloN1'], PASSWORD_DEFAULT);
            $this->pracownikModel->zmienHaslo($id, $haslo);

            $this->zalogujPracownika($id);
          }
        }
      }

      $this->view('pracownicy/zmien_haslo', $data);
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

     // tylko admin
     sprawdzCzyPosiadaDostep(-1,-1);

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

     // tylko admin
     sprawdzCzyPosiadaDostep(-1,-1);

     $pracownik = $this->pracownikModel->pobierzPracownikaPoId($id);
     $haslo = password_hash($pracownik->login, PASSWORD_DEFAULT);

     $this->pracownikModel->zmienHaslo($id, $haslo);
     $wiadomosc = "Hasło pracownika zostało ustawione na domyślne.";
     flash('pracownicy_wiadomosc', $wiadomosc);
     redirect('pracownicy/zestawienie'); 
   }



   /*
    * FUNKCJE POMOCNICZE
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

      // php 5.3 +
      $teraz = new DateTime();
      $oz = new DateTime($zmiana);
      $roznica = $oz->diff($teraz);

      return ($roznica->d > $limit);
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
      $_SESSION['poziom'] = $this->pracownikModel->pobierzPoziomDostepu($id);
      redirect('pages'); 
   }



   /*
    * FUNKCJE DOTYCZĄCE ADMINA
    */

    public function dodaj_admin() {
      /*
       * Obsługuje proces dodawania admina do systemu.
       * Zasada działania jak w funkcji dodaj.
       *
       * Proces dodawania admina sprowadza się do podania hasła
       * i jego powtórzenia.
       * Login ustawiony jest automatycznie jako 'admin'.
       * Admin posiada osobną tabelę w bazie danych i osobny model Admin.
       *
       * Obsługuje widok: pracownicy/dodaj_admin
       */

      $data = [
        'title' => 'Utwórz konto admina',
        'istnieje' => $this->adminModel->czyIstniejeAdmin(),
        'haslo1' => '',
        'haslo2' => '',
        'haslo1_err' => '',
        'haslo2_err' => '',
      ];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data['haslo1'] = trim($_POST['haslo1']);
        $data['haslo2'] = $_POST['haslo2'];

        $data['haslo1_err'] = $this->validator->sprawdzDlugosc($data['haslo1'], 8);
        if ($data['haslo1'] != $data['haslo2']) {
          $data['haslo2_err'] = 'Podane hasła się różnią';
        }

        if (empty($data['haslo1_err']) &&
            empty($data['haslo2_err'])) {

          $data['haslo1'] = password_hash($data['haslo1'], PASSWORD_DEFAULT);

          $this->adminModel->dodajAdmina($data);
          $data['istnieje'] = true;

          // twórz wiadomość do wyświetlenia po przekierowaniu
          $wiadomosc = "Konto admina zostało utworzone";
          flash('admin_wiadomosc', $wiadomosc);
          redirect('pracownicy/dodaj_admin'); 
        }
      }

      $this->view('pracownicy/dodaj_admin', $data);
    }

    private function logowanieAdmina($data) {
      /*
       * Obsługuje logowanie admina.
       * Polega na sprawdzeniu zgodności hasła -
       * login jest znany - w systemie jest tylko jeden admin.
       *
       * Niewłaściwe hasło powoduje powrót do formularza z komunikatem 
       * błędu. Pozytywne logowanie ustawia dane sesji i przekierowuje
       * na stronę ustawień ogólnych.
       *
       * Parametry:
       *  - data => dane z formularza logowania
       * Zwraca:
       *  - brak
       */

       if ($this->adminModel->czyPoprawneHaslo($data['haslo'])) {
         $this->zalogujAdmina();
       } else {
         // hasło błędne
         $data['haslo_err'] = "Podano niepoprawne hasło";
         $this->view('pracownicy/zaloguj', $data);
       }
    }

    private function zmienHasloAdmina($data) {
      /*
       * Obsługuje zmianę hasła admina.
       * Stare hasło musi być zgodne z tym w bazie.
       * Nowe hasła muszą być takie same i różne od istniejącego.
       *
       * Błedy powodują powrót do formularza z komunikatem błędu.
       * Pozytywne logowanie ustawia dane sesji i przekierowuje
       * na stronę ustawień ogólnych.
       *
       * Parametry:
       *  - data => dane z formularza zmiany hasła
       * Zwraca:
       *  - brak
       */

      if (!$this->adminModel->czyPoprawneHaslo($data['hasloS'])) {
        $data['hasloS_err'] = "Podano niepoprawne hasło";
      }

      $data['hasloN1_err'] = $this->validator->sprawdzNoweHaslo($data['hasloN1'], $data['hasloS'], 8);
      if ($data['hasloN2'] != $data['hasloN1']) {
        $data['hasloN2_err'] = 'Podane hasła się różnią';
      }

      if (empty($data['hasloS_err']) &&
          empty($data['hasloN1_err']) &&
          empty($data['hasloN2_err'])) {

        $haslo = password_hash($data['hasloN1'], PASSWORD_DEFAULT);
        $this->adminModel->zmienHaslo($haslo);

        $this->zalogujAdmina();

      } else {
        $this->view('pracownicy/zmien_haslo', $data);
      }
    }

    private function zalogujAdmina() {
      /*
       * Funkcja pomocnicza - ustawia dane sesji.
       *
       * Parametry:
       *  - brak
       * Zwraca:
       *  - brak
       */

        // specjalna wastość dla admina
        $_SESSION['user_id'] = 0;
        $_SESSION['imie_nazwisko'] = 'admin';
        $_SESSION['poziom'] = -1;
        // tymczasowo
        redirect('pracownicy/zestawienie');
    }


  }
