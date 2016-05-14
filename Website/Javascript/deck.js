/* cookie name root*/
const $cookie_name_root = "cardName_";
const $cookie_expires_days = 30;
/* cookie count*/
$cookie_count = 0;
/* cards array */
$cards_array = [];

$is_an_event_running = false;

/* write a new cookie */
function createCookie(cookie_name, cookie_value, expires_days) {
	var path = "/";
	var data = new Date();
    data.setTime(data.getTime() + (expires_days*(24*60*60*1000)));
    var expires = "expires="+data.toUTCString();
    document.cookie = cookie_name + "=" + cookie_value + "; " + expires + "; " + path;
}

/* delete all cookies */
function deleteAllCookies(){
	var cookies = document.cookie.split(';');
	for(var i=0; i<cookies.length; i++){
		var name = $cookie_name_root + i + "=";
		createCookie(name, "", -1);
	}
}

/* read all cookies */
function getCookies() {
	$cards_array = [];
    var cookies = document.cookie.split(';');
    for(var i=0; i<cookies.length; i++) {
		var name = $cookie_name_root + i + "=";
        var temp_element = cookies[i];
		/* deleting initial spaces */
        while (temp_element.charAt(0)==' ')
			temp_element = temp_element.substring(1);
		/* adding the id to the array */
        if (temp_element.indexOf(name) == 0){
			var card = temp_element.substring(name.length,temp_element.length).split('~');
			$cards_array[i] = {id: card[0], name: card[1]};
		}
		
    }
}


/* saving on cookies the whole deck */
function onSaveBtnClick(){
	if(!$is_an_event_running){
		$is_an_event_running = true;
		deleteAllCookies();
		for(i=0; i<$cards_array.length; i++){
			cookie_name = $cookie_name_root + i;
			createCookie(cookie_name ,$cards_array[i].id + "~" + $cards_array[i].name, $cookie_expires_days);
		}
		$is_an_event_running = false;
	}
}

/* load a deck from cookies */
function onLoadBtnClick(){
	if(!$is_an_event_running){
		$is_an_event_running = true;
		getCookies();
		document.getElementById("div-deck-container").innerHTML = "";
		for(i = 0; i < $cards_array.length; i++){
			display_card_name($cards_array[i].id, $cards_array[i].name);
		}
		$is_an_event_running = false;
	}
}


/* add a card in the deck */
function add_card(btn){
	if(!$is_an_event_running){
		$is_an_event_running = true;
		var card_elements = btn.parentElement.parentElement.childNodes;
		var i = 0;
		/* getting the card name */
		while(card_elements[i].className != 'card-name' && i < card_elements.length){
			i++;
		}
		if(i != card_elements.length){
			var card_name = card_elements[i].innerHTML;
			var card_id = card_elements[i].id;
			/* add the card to the deck only if there are at maximum 4 same cards */
			if(card_id != "" && card_name != "" && card_count(card_name) < 4){
				$cards_array[$cards_array.length] = {id:card_id, name:card_name};
				display_card_name(card_id, card_name);
			}
			else
				alert("Hai già inserito 4 volte questa carta!");
		}else{
			alert("Si è verificato un problema. Ci scusiamo per il disagio");
		}
		
		$is_an_event_running = false;
	}
}


/* function that display the card name in a new paragraph */
function display_card_name(id, name){
	$("#div-deck-container").append("<p class='deck_card' data-name='" + name + "' data-value='" + id + "'><a class='card-link' href='#'>" + name + "</a></p>");
}


/* function that counts the recurrence of a card */
function card_count(card_name){
	var count = 0;
	for(var i = 0; i<$cards_array.length; i++){
		if($cards_array[i].name == card_name) count++;
	}
	return count;
}



$(document).ready(function() {
	$(document).on("click", ".deck_card", function(){
		var cardName = $(this).data('name');
		var cardId = $(this).data('value');
		$.ajax({
			url: "Php/ricerca.php",
			type: "GET",
			data: {"id": cardId, "nome": cardName, "tipo": "", "colore": ""},
			success:function(result){
				var card = $.parseJSON(result);
				if(card != null){
					card_info = {id:card[0].id,
								name:card[0].nome,
								color:card[0].colore,
								type:card[0].tipo,
								link_immagine:card[0].link_immagine};
					if(card.link_immagine=="nolink")
						card.link_immagine = PATHIMGDEFAULT;
					$("#card_information").html('');
					$("#card_information").append("<div class='card'><div><div class='thumbnail'><img class='card-img' src='" + card_info.link_immagine + "'><div class='caption'><h3 class='card-name' id='" + card_info.id + "'>" + card_info.name + "</h3><p class='card-color'>colore: " + card_info.color + "</p><p class='card-type'>tipo: " + card_info.type + "</p><p><a href='#' class='btn btn-primary btn-add-card' onclick='remove_card(this)' role='button'>Rimuovi dal mazzo</a></p><p><a href='#' class='btn btn-primary btn-add-card' onclick='close_popup()' role='button'>Chiudi</a></p></div></div></div></div>");
					$("#card_information").removeClass("hidden-popup");
					$("#card_information").addClass("visible-popup");
				}else
					alert("Si è verificato un errore. Ci scusiamo per l'inconveniente!");
			},
			error: function(richiesta,stato,errori){
				alert("Impossibile connettersi, riprova più tardi! Ci scusiamo per il disagio");
			}
		})
	});
});

function close_popup(){
	if(!$is_an_event_running){
		$is_an_event_running = true;
		$("#card_information").removeClass("visible-popup");
		$("#card_information").addClass("hidden-popup");
		$is_an_event_running = false;
	}
}

function remove_card(btn){
	if(!$is_an_event_running){
		$is_an_event_running = true;
		
		var card_elements = btn.parentElement.parentElement.childNodes;
		var i = 0;
		/* getting the card name */
		while(card_elements[i].className != 'card-name' && i < card_elements.length){
			i++;
		}
		if(i != card_elements.length){
			var card_name = card_elements[i].innerHTML;
			var card_id = card_elements[i].id;
			/* remove the card from the deck */
			if(card_id != "" && card_name != ""){
				var found = false;
				for(i = 0; i < $cards_array.length && !found; i++){
					if($cards_array[i].id == card_id && $cards_array[i].name == card_name){
						found = false;
						$cards_array.splice(i, 1);
					}
				}
				document.getElementById("div-deck-container").innerHTML = "";
				for(i = 0; i < $cards_array.length; i++){
					display_card_name($cards_array[i].id, $cards_array[i].name);
				}
			}
		}else{
			alert("Si è verificato un problema. Ci scusiamo per il disagio");
		}
		
		$("#card_information").removeClass("visible-popup");
		$("#card_information").addClass("hidden-popup");
		$is_an_event_running = false;
	}
}