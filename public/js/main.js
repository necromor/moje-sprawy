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


// obsługa guzików do wyświetlenia szczegółów korespondencji
$('.pokazSzczegoly').click(function () {
  let idGuzika = this.id;
  let podzial = idGuzika.split("-");
  $('#szczegolyKorespondencji').html(showLoader());
  switch (podzial[0]) {
    case '1':
      szczegolyPrzychodzace(podzial[1]);
      break;
    case '2':
      szczegolyWychodzace(podzial[1]);
      break;
    case '3':
      szczegolyInne(podzial[1]);
      break;
  }

  // otwórz details operacji
  $('#detPisma').prop('open', true);
});

function szczegolyPrzychodzace(id) {
  // wysyła zapytanie ajax
  // wstawia otrzymane dane do div szczegolyKorespondencji

  // wyślij zapytanie ajax
  $.ajax({
    type: 'GET',
    url: URLROOT + 'przychodzace/ajax_przychodzace/' + id
  })
    .done(function(data) {
      const pismo = JSON.parse(data);
      // jeżeli id = -1 to znaczy, że pismo nie istnieje
      if (pismo.id != '-1') {
        // wstaw informacje do diva
        $('#szczegolyKorespondencji').html(tworzHtmlPrzychodzace(pismo));
      }
  });
}

function szczegolyWychodzace(id) {
  console.log(id);
}

function szczegolyInne(id) {
  console.log(id);
}

function tworzHtmlPrzychodzace(pismo) {
  // tworzy html na podstawie danych pisma przychodzącego
  let html = '<ul class="list-group list-group-flush">';
  html+= `<li class="list-group-item"><u>nr rej.</u>: ${pismo['nr_rejestru']}</li>`;
  html+= `<li class="list-group-item"><u>znak</u>: ${pismo['znak']}</li>`;
  html+= `<li class="list-group-item"><u>data pisma</u>: ${pismo['data_pisma']}</li>`;
  html+= `<li class="list-group-item"><u>data wpływu</u>: ${pismo['data_wplywu']}</li>`;
  html+= `<li class="list-group-item"><u>nadawca</u>: ${pismo['nazwa']}</li>`;
  html+= `<li class="list-group-item"><u>dotyczy</u>: ${pismo['dotyczy']}</li>`;
  html+= `<li class="list-group-item"><u>liczba zał.</u>: ${pismo['liczba_zalacznikow']}</li>`;
  html+= '</dl>';
  return html;
}

function showLoader() {
  return `<img src="${URLROOT}img/loader.gif" alt="Pobieram dane..." style="display:block; margin: 0.5em auto">`;
}

