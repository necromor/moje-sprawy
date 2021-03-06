<?php
  /*
   * Model Wychodzące obsługuje wszystkie funkcje związane
   * z korespondencją wychodząca.
   * Funkcje te obejmują:
   *  - dodawanie nowej korespondencji w ramach sprawy
   *  - zwykłe pismo lub decyzja lub postanowienie (osobne modele)
   *  - edycję istniejącej korespondencji
   *  - tworzenie zestawień korespondencji
   *
   *  Sposób wyjścia pisma:
   *  0 - pismo nie wyszło (data jest NULL)
   *  1 - odebrane osobiście
   *  2 - wysłane pocztą - dodatkowe numery mogą służyć do określenia sposobu przesyłki
   *
   */

  class Wychodzaca {

    private $db;

    public function __construct() {
      /*
       * Konstruktor klasy - tworzy połączenie z bazą danych
       */

      $this->db = new Database;
    }

    public function pobierzWychodzace($rok) {
      /*
       * Pobiera wszystkie dane o pismach wychodzących dodanych w danym roku.
       *
       * Parametry:
       *  - rok => rok dodania pisma do bazy danych
       * Zwraca:
       *  - set zawierający dane pism
       */

      $sql = "SELECT wychodzace.*,
                     postanowienia.id AS postanowienieId,
                     postanowienia.numer AS postanowienieNumer,
                     postanowienia.dotyczy AS postanowienieDotyczy
                FROM
                  (SELECT wychodzace.*,
                     decyzje.id AS decyzjaId,
                     decyzje.numer AS decyzjaNumer,
                     decyzje.dotyczy AS decyzjaDotyczy
                FROM
                  (SELECT
                     wychodzace.*,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy
                     WHERE wychodzace.utworzone LIKE :rok
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id)wychodzace
                LEFT OUTER JOIN decyzje ON wychodzace.id=decyzje.id_wychodzace)wychodzace
                LEFT OUTER JOIN postanowienia ON wychodzace.id=postanowienia.id_wychodzace
                ORDER BY wychodzace.znak ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . '%');

      return $this->db->resultSet();
    }


    public function pobierzWychodzacaPoId($id) {
      /*
       * Pobiera wszystkie dane o korespondencji na podstawie podanego id.
       *
       * Parametry:
       *  - id => id szukanej korespondencji
       * Zwraca:
       *  - wiersz z bazy zawierający wszystkie dane korespondencji
       */

      $sql = "SELECT wychodzace.*,
                     postanowienia.id AS postanowienieId,
                     postanowienia.numer AS postanowienieNumer,
                     postanowienia.dotyczy AS postanowienieDotyczy
                FROM
                  (SELECT wychodzace.*,
                     decyzje.id AS decyzjaId,
                     decyzje.numer AS decyzjaNumer,
                     decyzje.dotyczy AS decyzjaDotyczy
                FROM
                  (SELECT
                     wychodzace.*,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy
                     WHERE wychodzace.id=:id
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id)wychodzace
                LEFT OUTER JOIN decyzje ON wychodzace.id=decyzje.id_wychodzace)wychodzace
                LEFT OUTER JOIN postanowienia ON wychodzace.id=postanowienia.id_wychodzace";
      $this->db->query($sql);
      $this->db->bind(':id', $id);

      return $this->db->single();
    }

    public function dodajWychodzaca($data) {
      /*
       * Dodaje pismo wychodzące do bazy danych.
       * Data pisma to data utworzenia rekordu w bazie danych.
       *
       * Parametry:
       *  - data => tablica zawierająca dane nowego pisma
       * Zwraca:
       *  - id dodanego pisma
       */

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "INSERT INTO wychodzace (id_sprawa, id_podmiot, dotyczy)
              VALUES (:id_sprawa, :id_podmiot, :dotyczy)";
      $this->db->query($sql);
      $this->db->bind(':id_sprawa', $data['id']);
      $this->db->bind(':id_podmiot', $id_podmiot);
      $this->db->bind(':dotyczy', $data['dotyczy']);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      if ($this->db->execute()) {
        $sql = "SELECT id FROM wychodzace
                  WHERE id_sprawa=:id_sprawa
                    AND id_podmiot=:id_podmiot
                    AND dotyczy=:dotyczy
                  ORDER BY utworzone DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id_sprawa', $data['id']);
        $this->db->bind(':id_podmiot', $id_podmiot);
        $this->db->bind(':dotyczy', $data['dotyczy']);
        $row = $this->db->single();
        return $row->id;
      }
    }

    public function edytujWychodzaca($data) {
      /*
       * Zmienia dane istniejącegeo pisma.
       * W danych wejściowych podane jest id pisma do zmiany.
       * W celu ułatwienia funkcja nie sprawdza które dane są faktycznie nowe
       * ale wstawia wszystkie pochodzące z formularza.
       *
       * WAŻNE:
       * Data pisma czyli utworzone nie podlega zmianie!
       * Czy sprawa ulega zmianie to DO USTALENIA.
       *
       * Parametry:
       *  - data => tablica zawierająca dane istniejącego pisma
       * Zwraca:
       *  - boolean
       */

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "UPDATE wychodzace SET dotyczy=:dotyczy, id_podmiot=:id_podmiot WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':id_podmiot', $id_podmiot);

      // zmień dane w bazie
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function oznaczWyslane($id, $rodzaj) {
      /*
       * Ustawia datę odbioru/wysyłki pisma wychodzącego.
       * Data odbioru/wysyłki to data obecna.
       *
       * Parametry:
       *  - id => id pisma odbieranego/wysyłanego
       *  - rodzaj => sposób odbioru / wysyłki - 1 - osobiście, 2 - pocztą
       * Zwraca:
       *  - boolean
       */

      $data = date("Y-m-d H:i:s");

      $sql = "UPDATE wychodzace SET data_wyjscia=:data, sposob_wyjscia=:rodzaj WHERE id=:id";
      $this->db->query($sql);
      $this->db->bind(':id', $id);
      $this->db->bind(':data', $data);
      $this->db->bind(':rodzaj', $rodzaj);

      // zmień dane w bazie
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function szukajWychodzace($data) {
      /*
       * Szuka pism wychodzących na podstawie określonych kryterów.
       * Nie mamy wpływu ile kryterów jest podanych i jaka kombinacja więc
       * wszystkie wyszukiwania opierają się nie na = a LIKE.
       * Dla pól znak, nazwa i dotyczy zastosowano mechanizm %wartosc%, dla pozostałych wartosc%.
       *
       * Parametry:
       *  - data => zbiór danych z formularza, możliwe pola to:
       *             - znka sprawy
       *             - data pisma
       *             - treść dotyczy
       *             - nazwa odbiorcy (podmiotu)
       * Zwraca:
       *  - set zawierający dane pism wychodzących posortowane rosnąco według daty utworzenia
       */

      //$sql = "SELECT
      //        wychodzace.*,
      //        sprawy.znak,
      //        sprawy.id AS sprawaId,
      //        podmioty.nazwa,
      //        podmioty.adres_1,
      //        podmioty.adres_2
      //        FROM wychodzace, podmioty, sprawy
      //        WHERE
      //              sprawy.znak LIKE :znak
      //          AND wychodzace.utworzone LIKE :data_pisma
      //          AND dotyczy LIKE :dotyczy
      //          AND podmioty.nazwa LIKE :nazwa
      //          AND wychodzace.id_podmiot=podmioty.id
      //          AND wychodzace.id_sprawa=sprawy.id
      //        ORDER BY wychodzace.utworzone ASC";
      $sql = "SELECT wychodzace.*,
                     postanowienia.id AS postanowienieId,
                     postanowienia.numer AS postanowienieNumer,
                     postanowienia.dotyczy AS postanowienieDotyczy
                FROM
                  (SELECT wychodzace.*,
                     decyzje.id AS decyzjaId,
                     decyzje.numer AS decyzjaNumer,
                     decyzje.dotyczy AS decyzjaDotyczy
                FROM
                  (SELECT
                     wychodzace.*,
                     podmioty.nazwa,
                     podmioty.adres_1,
                     podmioty.adres_2,
                     sprawy.znak,
                     sprawy.id AS sprawaId
                     FROM wychodzace, podmioty, sprawy
                     WHERE
                           sprawy.znak LIKE :znak
                       AND wychodzace.utworzone LIKE :data_pisma
                       AND dotyczy LIKE :dotyczy
                       AND podmioty.nazwa LIKE :nazwa
                       AND wychodzace.id_podmiot=podmioty.id
                       AND wychodzace.id_sprawa=sprawy.id)wychodzace
                LEFT OUTER JOIN decyzje ON wychodzace.id=decyzje.id_wychodzace)wychodzace
                LEFT OUTER JOIN postanowienia ON wychodzace.id=postanowienia.id_wychodzace";
      $this->db->query($sql);
      $this->db->bind(':znak', '%' . $data['znak'] . '%');
      $this->db->bind(':data_pisma', $data['data_pisma'] . '%');
      $this->db->bind(':dotyczy', '%' . $data['dotyczy'] . '%');
      $this->db->bind(':nazwa', '%' . $data['nazwa'] . '%');

      return $this->db->resultSet();
    }

  }
