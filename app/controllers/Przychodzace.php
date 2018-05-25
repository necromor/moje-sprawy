<?php

  class Przychodzace extends Controller {

    public function __construct() {

      $this->przychodzacaModel = $this->model('Przychodzaca');
      $this->podmiotModel = $this->model('Podmiot');
      $this->pracownikModel = $this->model('Pracownik');
    }

    public function zestawienie($rok) {

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


   public function dodaj() {

      // tylko zalogowany sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $listaPodmiotow = $this->podmiotModel->pobierzPodmioty();
      $listaPracownikow = $this->pracownikModel->pobierzPracownikow();

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

        $data = [
          'title' => 'Dodaj korespondencję przychodzącą',
          'podmioty' => $listaPodmiotow,
          'pracownicy' => $listaPracownikow,
          'czy_nowy' => $_POST['czyNowy'],
          'podmiot_nazwa' => $nazwa,
          'podmiot_adres' => $adres,
          'podmiot_poczta' => $poczta,
          'znak' => trim($_POST['znak']),
          'data_pisma' => trim($_POST['dataPisma']),
          'data_wplywu' => trim($_POST['dataWplywu']),
          'dotyczy' => trim($_POST['dotyczy']),
          'czy_faktura' => $_POST['czyFaktura'],
          'liczba_zalacznikow' => trim($_POST['liczbaZalacznikow']),
          'dekretacja' => trim($_POST['dekretacja']),
          'kwota' => trim($_POST['kwota']),
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

        $data['podmiot_nazwa_err'] = $this->sprawdzNazwePodmiotu($data['podmiot_nazwa'], $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->sprawdzAdresPodmiotu($data['podmiot_adres'], $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->sprawdzPocztaPodmiotu($data['podmiot_poczta'], $data['czy_nowy']);
        $data['znak_err'] = $this->sprawdzZnak($data['znak']);
        $data['data_pisma_err'] = $this->sprawdzDatePisma($data['data_pisma']);
        $data['data_wplywu_err'] = $this->sprawdzDateWplywu($data['data_wplywu']);
        $data['dotyczy_err'] = $this->sprawdzDotyczy($data['dotyczy']);
        $data['liczba_zalacznikow_err'] = $this->sprawdzLiczbaZalacznikow($data['liczba_zalacznikow'], $data['czy_faktura']);
        $data['dekretacja_err'] = $this->sprawdzDekretacja($data['dekretacja'], $data['czy_faktura']);
        $data['kwota_err'] = $this->sprawdzKwota($data['kwota'], $data['czy_faktura']);

        // Dodaj do bazy danych gdy nie ma błędów
        if (empty($data['podmiot_nazwa_err']) && empty($data['podmiot_adres_err']) && empty($data['podmiot_poczta_err']) && empty($data['znak_err']) && empty($data['data_pisma_err']) && empty($data['data_wplywu_err']) && empty($data['dotyczy_err']) && empty($data['liczba_zalacznikow_err']) && empty($data['dekretacja_err']) && empty($data['kwota_err'])) {

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

          $numery = $this->przychodzacaModel->dodajPrzychodzaca($data);

          // utwórz wiadomość zwrotną w zależności do zostało dodane
          $wiadomosc = "Korespondencja dodana pomyślnie z numerem rejestru: <strong>" . $numery['nr_rejestru'] . "</strong>";
          if ($data['czy_faktura'] == '1') {
            $wiadomosc.= "<br>Numer w rejestrze faktur: <strong>" . $numery['nr_rejestru_faktur'] . "</strong>";
          }
          flash('korespondencja_dodaj', $wiadomosc);
          redirect('przychodzace/dodaj');

        } else {
          // wyświetl formularz z błędami
          $this->view('przychodzace/dodaj', $data);
        }

      } else {

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
 
        $this->view('przychodzace/dodaj', $data);
      }
   }


   public function edytuj($id) {

      // tylko zalogownay sekretariat
      sprawdzCzyPosiadaDostep(0,0);

      $listaPodmiotow = $this->podmiotModel->pobierzPodmioty();
      $listaPracownikow = $this->pracownikModel->pobierzPracownikow();

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

        $data = [
          'title' => 'Zmień dane korespondencji przychodzącej',
          'podmioty' => $listaPodmiotow,
          'pracownicy' => $listaPracownikow,
          'id' => $id,
          'czy_nowy' => $_POST['czyNowy'],
          'podmiot_nazwa' => $nazwa,
          'podmiot_adres' => $adres,
          'podmiot_poczta' => $poczta,
          'znak' => trim($_POST['znak']),
          'data_pisma' => trim($_POST['dataPisma']),
          'data_wplywu' => trim($_POST['dataWplywu']),
          'dotyczy' => trim($_POST['dotyczy']),
          'czy_faktura' => $_POST['czyFaktura'],
          'liczba_zalacznikow' => trim($_POST['liczbaZalacznikow']),
          'dekretacja' => trim($_POST['dekretacja']),
          'kwota' => trim($_POST['kwota']),
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

        $data['podmiot_nazwa_err'] = $this->sprawdzNazwePodmiotu($data['podmiot_nazwa'], $data['czy_nowy']);
        $data['podmiot_adres_err'] = $this->sprawdzAdresPodmiotu($data['podmiot_adres'], $data['czy_nowy']);
        $data['podmiot_poczta_err'] = $this->sprawdzPocztaPodmiotu($data['podmiot_poczta'], $data['czy_nowy']);
        $data['znak_err'] = $this->sprawdzZnak($data['znak']);
        $data['data_pisma_err'] = $this->sprawdzDatePisma($data['data_pisma']);
        $data['data_wplywu_err'] = $this->sprawdzDateWplywu($data['data_wplywu']);
        $data['dotyczy_err'] = $this->sprawdzDotyczy($data['dotyczy']);
        $data['liczba_zalacznikow_err'] = $this->sprawdzLiczbaZalacznikow($data['liczba_zalacznikow'], $data['czy_faktura']);
        $data['dekretacja_err'] = $this->sprawdzDekretacja($data['dekretacja'], $data['czy_faktura']);
        $data['kwota_err'] = $this->sprawdzKwota($data['kwota'], $data['czy_faktura']);

        // Dodaj do bazy danych gdy nie ma błędów
        if (empty($data['podmiot_nazwa_err']) && empty($data['podmiot_adres_err']) && empty($data['podmiot_poczta_err']) && empty($data['znak_err']) && empty($data['data_pisma_err']) && empty($data['data_wplywu_err']) && empty($data['dotyczy_err']) && empty($data['liczba_zalacznikow_err']) && empty($data['dekretacja_err']) && empty($data['kwota_err'])) {

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

        } else {
          // wyświetl formularz z błędami
          $this->view('przychodzace/edytuj', $data);
        }

      } else {

        $pismo = $this->przychodzacaModel->pobierzPrzychodzacaPoId($id);

        // zamień dane pracownika jeżeli to pismo nie faktura
        if($pismo->id_pracownik != 0) {
          $imie_nazwisko = $this->pracownikModel->pobierzImieNazwisko($pismo->id_pracownik);
          $pismo->id_pracownik = utworzIdNazwa($pismo->id_pracownik, $imie_nazwisko);
        }

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

        $this->view('przychodzace/edytuj', $data);
      }
   }

   /*
    * FUNKCJE POMOCNICE
    */

   private function pobierzPodmiot($nazwa) {

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

   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function sprawdzNazwePodmiotu($nazwa, $nowy) {

     $error = '';

     if ($nazwa == '') {
       $error = "Nazwa nie może pozostać pusta.";
     }

     return $error;
   }

   private function sprawdzAdresPodmiotu($adres, $nowy) {

     $error = '';
     // istniejący podmiot - wartość pola nie ma znaczenia 
     if ($nowy == '0') {
       return $error;
     }

     if ($adres == '') {
       $error = "Pole adresu nie może pozostać puste.";
     }

     return $error;
   }

   private function sprawdzPocztaPodmiotu($poczta, $nowy) {

     $error = '';
     // istniejący podmiot - wartość pola nie ma znaczenia 
     if ($nowy == '0') {
       return $error;
     }

     if ($poczta == '') {
       $error = "Pole poczty nie może pozostać puste.";
     }

     return $error;
   }

   private function sprawdzZnak($znak) {

     $error = '';
     if ($znak == '') {
       $error = "Oznaczenie pisma nie może pozostać puste.<br>Wpisz <em>brak</em> jeżeli pismo nie ma oznaczenia.";
     }

     return $error;
   }

   private function sprawdzDatePisma($data) {

     $error = '';
     if ($data == '') {
       $error = "Data pisma nie może pozostać pusta.<br>Wpisz <em>datę wpływu</em> jeżeli pismo nie ma daty.";
     }

     return $error;
   }

   private function sprawdzDateWplywu($data) {

     $error = '';
     if ($data == '') {
       $error = "Data wpływu nie może pozostać pusta.";
     }

     return $error;
   }

   private function sprawdzDotyczy($dotyczy) {

     $error = '';
     if ($dotyczy == '') {
       $error = "Każde pismo czegoś dotyczy.";
     }

     return $error;
   }

   private function sprawdzLiczbaZalacznikow($lzal, $czyFaktura) {

     $error = '';
     // dodawana faktura - wartość pola nie ma znaczenia
     if ($czyFaktura == '1') {
       return $error;
     }

     if ($lzal == '') {
       $error = "Wpisz 0 jeżeli pismo nie ma załączników.";
     }

     return $error;
   }

   private function sprawdzDekretacja($dekretacja, $czyFaktura) {

     $error = '';
     // dodawana faktura - wartość pola nie ma znaczenia
     if ($czyFaktura == '1') {
       return $error;
     }

     if ($dekretacja == '') {
       $error = "Każde pismo posiada dekretację.";
     }

     return $error;
   }

   private function sprawdzKwota($kwota, $czyFaktura) {

     $error = '';

     // dodawane pismo - wartość pola nie ma znaczenia
     if ($czyFaktura == '1') {
       return $error;
     }

     if ($kwota == '') {
       $error = "Każda faktura posiada kwotę.";
     }

     return $error;
   }


  }
