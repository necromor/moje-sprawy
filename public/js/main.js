// zmienna do adresów ajax
const URLROOT = 'http://ms.test/';

// na dzień dobry ukryj pola w zależności które radio jest zaznaczone
if ($('#radioPismo').is(':checked')) {
    $('#fakturaRow1').hide();
} else {
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
}

// na dzień dobry ukryj pola grupy
if ($('#radioPojedynczy').is(':checked')) {
    $('#jrwaGrupa').hide();
} else {
    $('#jrwaPojedynczy1').hide();
    $('#jrwaPojedynczy2').hide();
}

// na dzień dobry ukryj pola decyzji/postanowienia
if ($('#radioDP0').is(':checked')) {
    $('.wychodzace-dp').hide();
} else {
    $('.wychodzace-dp').show();
}


// obsługa zmiany opcji pismo czy faktura
$('input[name=czyFaktura]:radio').on('click', function(e) {
  if (e.target.id == 'radioFaktura') {
    // ukryj pola dotyczące pisma
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
    // pokaż pole dotyczące faktury
    $('#fakturaRow1').show();
    // ustaw domyślne wartości
    $('#liczbaZalacznikow').val('0');
    $('#dekretacja').val('');
  } else {
    // pokaż pola dotyczące pisma
    $('#pismoRow1').show();
    $('#pismoRow2').show();
    // ukryj pole dotyczące faktury
    $('#fakturaRow1').hide();
    // ustaw domyślną wartość
    $('#kwota').val('0.00');
  }
});

// obsługa zmiany opcji istniejący / nowy podmiot
$('input[name=czyNowy]:radio').on('click', function(e) {
  if (e.target.id == 'radioIstniejacy') {
    // zablokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', true);
    $('#pocztaPodmiotu').prop('disabled', true);
  } else {
    // odblokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', false);
    $('#pocztaPodmiotu').prop('disabled', false);
  }
    // wyczyść wszystkie pola
    $('#nazwaPodmiotu').val('');
    $('#adresPodmiotu').val('');
    $('#pocztaPodmiotu').val('');
});

// obsługa zmiany opcji pojedyczny numer / grupa numerów jrwa
$('input[name=czyGrupa]:radio').on('click', function(e) {
  if (e.target.id == 'radioGrupa') {
    // ukryj pola dotyczące pojedynczego numeru
    $('#jrwaPojedynczy1').hide();
    $('#jrwaPojedynczy2').hide();
    // pokaż pole dotyczące faktury
    $('#jrwaGrupa').show();
  } else {
    // ukryj pola dotyczące pojedynczego numeru
    $('#jrwaPojedynczy1').show();
    $('#jrwaPojedynczy2').show();
    // pokaż pole dotyczące faktury
    $('#jrwaGrupa').hide();
  }
});

// obsługa zmiany opcji zwykłe pismo / decyzja / postanowienie
$('input[name=radioDP]:radio').on('click', function(e) {
  if (e.target.id == 'radioDP0') {
    // ukryj dodatkowe pola
    $('.wychodzace-dp').hide();
  } else {
    // pokaż dodatkowe pola
    $('.wychodzace-dp').show();

    // wyczyść pola
    $('#oznaczenieDP').val('Pobieram kolejny numer...');
    $('#dotyczyDP').val('');

    // pobierz kolejny numer decyzji / postanowienia
    const jrwa = $('#idJrwa').val();
    let adres = URLROOT + 'decyzje/ajax_numer_kolejny/' + jrwa;
    if (e.target.id == 'radioDP2') {
      adres = URLROOT + 'postanowienia/ajax_numer_kolejny/' + jrwa;
    }
    $.ajax({
      type: 'GET',
      url: adres
    })
      .done(function(data) {
        const o = JSON.parse(data);
          $('#oznaczenieDP').val(o.numer);
      });
  }
});

// potwierdzenie kliknięcia w link resetu
$('.reset').on('click', function() {
  return confirm('Operacja jest nieodwracalna!. Jesteś pewny?');
});


// obsługa wyboru podmiotu z listy
// wyślij zapytanie ajax i wstaw otrzymane dane do pól adresu i poczty
$('#nazwaPodmiotu').on('change', function() {
  // tylko gdy dotyczy istniejącego podmiotu i wartość pola jest niepusta
  if ($('#radioIstniejacy').is(':checked') && $('#nazwaPodmiotu').val() != '') {
    let nazwa = $('#nazwaPodmiotu').val();
    let podzial = nazwa.split("#");
    // nie chcemy string w adresie
    let id = parseInt(podzial[0]);

    // wyślij zapytanie ajax
    $.ajax({
      type: 'GET',
      url: URLROOT + 'podmioty/ajax_podmiot/' + id
    })
      .done(function(data) {
        const podmiot = JSON.parse(data);
        // jeżeli id = -1 to znaczy, że podmiot nie istnieje
        if (podmiot.id != '-1') {
          // wstaw dane adresowe do pól
          $('#adresPodmiotu').val(podmiot.adres_1);
          $('#pocztaPodmiotu').val(podmiot.adres_2);
        } else {
          // wyczyść pola - na wszelki wypadek
          $('#adresPodmiotu').val('');
          $('#pocztaPodmiotu').val('');
        }
    });
  } else {
    // wyczyść pola - na wszelki wypadek
    $('#adresPodmiotu').val('');
    $('#pocztaPodmiotu').val('');
  }
});

// obsługa wyboru numeru jrwa z listy
// wyślij zapytanie ajax i wstaw otrzymany opis do paragrafu opisJrwa
$('#jrwa').on('change', function() {

  //$('#opisJrwa').text('Pobieram opis wybranego numeru...');
  $('#opisJrwa').html(showLoader());
  let numer = $('#jrwa').val();

  // wyślij zapytanie ajax
  $.ajax({
    type: 'GET',
    url: URLROOT + 'jrwa/ajax_jrwa/' + numer
  })
    .done(function(data) {
      const jrwa = JSON.parse(data);
      // jeżeli id = -1 to znaczy, że numer jrwa nie istnieje
      if (jrwa.id != '-1') {
        // wstaw dane adresowe do pól
        $('#opisJrwa').text(jrwa.opis);
      } else {
        // wyczyść pola - na wszelki wypadek
        $('#opisJrwa').text('Podany numer nie istnieje.');
      }
  });
});



function showLoader() {
  return `<img src="${URLROOT}img/loader.gif" alt="Pobieram dane..." style="display:block; margin: 0.5em auto">`;
}

// obsługa filtrowania listy przy wyborze sprawy
$('#filtrujListe').click(function() {
  usunKlasyFiltrujListe();
  const info = $('#filtrujInfo');
  info.addClass('alert-info');
  info.text('Trwa pobieranie danych...');

  let jrwa = '';
  const rok = $('#filtrujRok').val();
  let numer = $('#filtrujJrwa').val();
  if (numer) {
    jrwa = '/' + numer
  }

  // wyślij zapytanie ajax
  $.ajax({
    type: 'GET',
    url: URLROOT + 'sprawy/ajax_lista/' + rok + jrwa
  })
    .done(function(data) {
      usunKlasyFiltrujListe();
      const lista = JSON.parse(data);

      // jeżeli lista pusta wyświetl informację
      if (lista.length == 0) {
        info.addClass('alert-warning');
        info.text('Zapytanie zakończone powodzeniem, ale w wybranym zakresie nie ma spraw.');
        ustawListeNumerowSpraw(lista);
        return;
      }

      // jeżeli był błąd jrwa
      if (lista[0]['znak'] == "-1") {
        info.addClass('alert-danger');
        info.text('Podany numer jrwa jest błędny!');
        return;
      }

      // zapytanie ma dane więc podmień listę
      info.addClass('alert-success');
      info.text('Lista została zaktualizowana.');
      ustawListeNumerowSpraw(lista);
  });
});

// usuń wszystkie klasy typu alert-... ze znacznika o id filtrujListe
function usunKlasyFiltrujListe() {
  const info = $('#filtrujInfo');
  info.removeClass('alert-info');
  info.removeClass('alert-success');
  info.removeClass('alert-warning');
  info.removeClass('alert-danger');
}

// ustawia numery spraw w liście na podstawie otrzymanego json
function ustawListeNumerowSpraw(numery) {
  let html = '';
  if (numery.length != 0) {
    numery.forEach(function(el) {
      html+= '<option value="' + el['znak']  + '">';
    });
  }
  $('#listaSpraw').html(html);
}

