<?php

  class Pracownicy extends Controller {

    private $POZIOMY = [
      0 => 'sekretariat',
      1 => 'księgowość',
      2 => 'zwykły'
    ];

    public function __construct() {

      $this->pracownikModel = $this->model('Pracownik');
    }


    public function zestawienie() {

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

          $wiadomosc = "Pracownik <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> został dodany pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 

        } else {
          $this->view('pracownicy/dodaj', $data);
        }

      } else {

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
          $this->view('pracownicy/edytuj', $data);
        }

      } else {

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
          $this->view('pracownicy/zmien_haslo', $data);
        }

      } else {

        //$pracownik = $this->pracownikModel->pobierzPracownikaPoId($id);

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

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć imię.";
     } elseif (strlen($tekst) < 2) {
       $error = "Imię musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzNazwisko($tekst) {

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć nazwisko.";
     } elseif (strlen($tekst) < 2) {
       $error = "Nazwisko musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzLogin($tekst, $id=0) {

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

     $error = '';

     if ($haslo == '') {
       $error = "Musisz podać stare hasło.";
     } elseif (!$this->pracownikModel->sprawdzHaslo($haslo, $id)) {
       $error = "Podano nieporawne hasło.";
     }

     return $error;
   }

   private function sprawdzNoweHaslo($haslo) {

     $error = '';

     if ($haslo == '') {
       $error = "Musisz podać nowe hasło.";
     } elseif (strlen($haslo) < 6) {
       $error = "Hasło musi mieć przynajmniej 6 znaków.";
     }

     return $error;
   }


  }
