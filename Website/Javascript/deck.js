/* cookie name root*/
const $cookie_name_root = "cardName_";
const $cookie_expires_days = 30;
/* cookie count*/
$cookie_count = 0;
/* cards array */
$cards_array;

/* write a new cookie */
function createCookie(cookie_name, cookie_value, expires_days) {
	var path = "/";
	var data = new Date();
    data.setTime(data.getTime() + (expires_days*(24*60*60*1000)));
    var expires = "expires="+data.toUTCString();
    document.cookie = cookie_name + "=" + cookie_value + "; " + expires + "; " + path;
	$cookie_count += 1;
}

/* read all cookies */
function getCookies() {
	var card_names;
    var cookies = document.cookie.split(';');
    for(var i=0; i<cookies.length; i++) {
		var name = $cookie_name_root + i + "=";
        var temp_element = cookies[i];
		/* deleting initial spaces */
        while (temp_element.charAt(0)==' ')
			temp_element = temp_element.substring(1);
		/* adding the id to the array */
        if (temp_element.indexOf(name) == 0)
			card_names[i] temp_element.substring(name.length,temp_element.length);
    }
    return card_names;
}

/* delete all cookies */
function deleteAllCookies(){
	for(var i=0; i<$cookies.length; i++){
		var name = $cookie_name_root + i + "=";
		createCookie(name, "", -1");
	}
	$cookie_count = 0;
}


/* saving on cookies the whole deck */
function onSaveBtnClick(){
	deleteAllCookies();
	for(var i=0; i<$cards_array.length; i++){
		cookie_name = $cookie_name_root + i;
		createCookie(cookie_name ,cards_array[i], $cookie_expires_days);
	}
}

/* load a deck from cookies */
function onLoadBtnClick(){
	cards=getCookies();
	document.getElementById("div-deck-container").innerHTML = "";
	for(var i=0; i<cards.length; i++)
		display_card_name(cards[i]);
}


/* add a card in the deck */
$('.btn-add-card').on('click', function (){
	name = $(this).parent.parent.next("h3.card-name").innerText;
	/* add the card to the deck only if there are at maximum 4 same cards */
	if(card_count(name)<4)
		display_card_name(name);
	else
		alert("Hai già inserito 4 volte questa carta!");
});


/* function that display the card name in a new paragraph */
function display_card_name(name){
	$cards_array[$cards_array.length] = name;
	$("#div-deck-container").append("<p>" + $cards_array[$cards_array.length] + "</p>");
}


/* function that counts the recurrence of a card */
function card_count(card_name){
	var count = 0;
	for(var i=0; i<$cards_array.length; i++){
		if(cards_array[i] == card_name) count++;
	return count;
}