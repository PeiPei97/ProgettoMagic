/* how many cards I can show */
const MAXCARDS = 21;
const MAXBUTTONS = 4;
/* selected item */
const SELECTEDITEM = 'selected-item';
/* default card image */
const PATHIMGDEFAULT = "Immagini/cards/default-card.jpg";
/* inizialized filter variable	*/
$filter = "nome";
/* search page number */
$page_number = 0;
/* search page count */
$pages_count = 0;
/* card list */
$card_list = [];

$(document).ready( function() {
	/* changing option menu header and variable filter */
	$('.dropdown-menu li a').on('click', function() {
		$("#"+$filter).removeClass(SELECTEDITEM);
		$(this).addClass(SELECTEDITEM);
		$filter = $(this).attr('id');
	});

	/* on search button click*/
	$('#search-btn-submit').on('click', function() {
		var search_text = $("#search-text").val();
		if(search_text!=""){
			$card_list = [];
			$('#div-cards-container').html('');
			var ricerca_nome = "";
			var ricerca_tipo = "";
			var ricerca_colore = "";
			
			if($filter == "nome"){
				ricerca_nome = search_text;
			}else if($filter == "tipo"){
				ricerca_tipo = search_text;
			}else if($filter == "colore"){
				ricerca_colore = search_text;
			}
			
			$.ajax({
				url: "Php/ricerca.php",
				type: "GET",
				data: {"id": "", "nome": ricerca_nome, "tipo": ricerca_tipo, "colore": ricerca_colore},
				success:function(result){
					var cards = $.parseJSON(result);
					for(var i = 0; i < cards.length; i++){
						$card_list[i] = {id:cards[i].id,
										name:cards[i].nome,
										color:cards[i].colore,
										type:cards[i].tipo,
										img_path:cards[i].link_immagine}; 
						
						if($card_list[i].img_path=="nolink"){
							$card_list[i].img_path = PATHIMGDEFAULT;
						}
						
					}
					if(cards.length == 0){
						alert("Nessuna carta trovata");
					}else{
						$pages_count = Math.ceil(cards.length/MAXCARDS);
						$page_number = 1;
						showCards();
						showButtons();
					}
				},
				error: function(richiesta,stato,errori){
					alert("Impossibile connettersi, riprova più tardi! Ci scusiamo per il disagio");
				}
			})
		}else{
			alert("Ricerca non valida");
		}
	});
	
	function imgError(image) {
		image.onerror = "";
		image.src = PATHIMGDEFAULT;
		return true;
	}
});

function search_iterator(btn){
	if(btn.id == 'btn-next'){
		if($page_number != $pages_count){
			$page_number += 1;
			showCards();
			showButtons();
		}
	}else if(btn.id == 'btn-previous'){
		if($page_number != 1){
			$page_number -= 1;
			showCards();
			showButtons();
		}
	}else{
		$page_number = parseInt(btn.text);
		showCards();
		showButtons();
	}
}

function showCards(){
	$('#div-cards-container').html('');
	for(var i = (($page_number - 1) * MAXCARDS); i < ($page_number * MAXCARDS) && i < $card_list.length; i++){
		$("#div-cards-container").append("<div class='card'><div class='col-sm-6 col-md-4'><div class='thumbnail'><img class='card-img' src='" + $card_list[i].img_path + "' onerror='imgError(this)'><div class='caption'><h3 class='card-name' id='" + $card_list[i].id + "'>" + $card_list[i].name + "</h3><p class='card-color'>colore: " + $card_list[i].color + "</p><p class='card-type'>tipo: " + $card_list[i].type + "</p><p><a href='#' class='btn btn-primary btn-add-card' onclick='add_card(this)' role='button'>Aggiungi al mazzo</a></p></div></div></div></div>");
	}
}
	
function showButtons(){
	buttonStringToAppend = "<nav id='div-result-pages-buttons'><ul class='pagination'><li class='page-item'><a id='btn-previous' onclick='search_iterator(this)' class='page-link' href='#' aria-label='Previous'><span aria-hidden='true'>&laquo;</span><span class='sr-only'>Previous</span></a></li>";
	var i;
	if($page_number > ($pages_count - MAXBUTTONS)){
		if(($pages_count - MAXBUTTONS) > 0){
			/* I'm in one of the latest MAXBUTTONS pages */
			i = $pages_count - MAXBUTTONS;
		}else{
			/* There are less then MAXBUTTONS pages */
			i = 1;
		}		
	}else{
		i = ($page_number);
	}
	for(i; i <= $pages_count && i <= ($page_number + MAXBUTTONS); i++){
		buttonStringToAppend += "<li class='page-item";
		if(i == $page_number)
			buttonStringToAppend += " active";
		buttonStringToAppend += "'><a class='page-link' onclick='search_iterator(this)' href='#'>" + i + "</a></li><li class='page-item'>";
	}
	buttonStringToAppend += "<li class='page-item'><a id='btn-next' onclick='search_iterator(this)' class='page-link' href='#' aria-label='Next'><span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span></a></li></ul></nav>";
	
	$("#div-cards-container").append(buttonStringToAppend);
}