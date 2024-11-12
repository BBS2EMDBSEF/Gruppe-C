"use strict";

jQuery(document).ready(function() {

 /* $("#geburtstag").datepicker(
    {
      monthNames: ['Januar','Februar','März','April','Mai','Juni',
          'Juli','August','September','Oktober','November','Dezember'],
      dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
      prevText: 'zurück', prevStatus: '',
      nextText: 'Vor', nextStatus: '',
      dateFormat:'dd.mm.yy'
    }
  );
  */
  //---------------------------------
  function createElement(element) {
    let msg = "<span style='text-transform: capitalize;'>"+$(element).attr("name")+"</span>";
    $(element).before("<strong class='error'>"+msg+" ist ein Pflichtfeld</strong>");
    $(element).focus();
  }
  //--------------------------------- 
  $("#register").submit(function(event) {
				
    $(".error").remove();
    let gs = $("#geschlecht").val();
    let nn = $("#nachname").val();
    let vn = $("#vorname").val();
    let geb = $("#geburtstag").val();
    let tel = $("#telefon").val();
    let janein = $("input[name='janein']");

    let wunschtag = $("input[name='wunschtag']");
    
    let mail = $("#mail").val();
    let pwd = $("#passwort").val();

  /*let wunschWert = "";
    for(let wert of wunschtag){
      if(wert.checked === true){
        wunschWert += wert.value;
      }
    }
    console.log(wunschWert);
   */

    
    let nameMuster = /^[a-z\.\-\_äöüß]{2,}$/i;
    let telMuster = /^(\+|0{2})\d{2}\s\d{2}\s\d{5,10}$/;
    let mailMuster = /^[a-zA-Z0-9\-\.\_]{2,10}@[a-z\-]{3,10}\.[a-z]{2,5}$/;
	  let passMuster = /^[a-zA-Z0-9\.\-\_äöüß]{6,}$/i;


		
		let isValid = true;
   
    if(gs === "0") {
      createElement("#geschlecht");
      isValid = false;
    }
    else if(nn === "" || !nameMuster.test(nachname.value)) {
      createElement("#nachname");
      isValid = false;
    }
    else if(vn === "" || !nameMuster.test(vorname.value)) {
      createElement("#vorname");
      isValid = false;
    }
    else if(geb === "") {
      createElement("#geburtstag");
      isValid = false;
    }
    else if(tel === "" || !telMuster.test(telefon.value)) {
      createElement("#telefon");
      isValid = false;
    }
    
    else if(!(janein[0].checked || janein[1].checked)) {
      createElement("#warenByUns");
      isValid = false;
    }
    else if(!(wunschtag[0].checked || wunschtag[1].checked || wunschtag[2].checked || wunschtag[3].checked || wunschtag[4].checked)) {
      createElement("#wunschtag");
      isValid = false;
    }
    else if(pwd === "" || !passMuster.test(passwort.value)) {
      createElement("#passwort");
      isValid = false;
    }
    else if(mail === "" || !mailMuster.test(email.value)) {
      createElement("#mail");
      isValid = false;
    }
    
		if(!isValid) {
			event.preventDefault();
		}else {
			return;
		}
    
	
  });
	

});//ende ready