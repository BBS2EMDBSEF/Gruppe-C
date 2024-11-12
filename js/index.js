"use strict";

  //--------------------------------- 
  $("#register").submit(function(event) {
				
    $(".error").remove();
    let nn = $("#nachname").val();
    let vn = $("#vorname").val();
    
    let mail = $("#mail").val();
    let pwd = $("#passwort").val();

    
    let nameMuster = /^[a-z\.\-\_äöüß]{2,}$/i;
    let mailMuster = /^[a-zA-Z0-9\-\.\_]{2,10}@[a-z\-]{3,10}\.[a-z]{2,5}$/;
	  let passMuster = /^[a-zA-Z0-9\.\-\_äöüß]{6,}$/i;


		
		let isValid = true;
   
    if(nn === "" || !nameMuster.test(nachname.value)) {
      createElement("#nachname");
      isValid = false;
    }
    else if(vn === "" || !nameMuster.test(vorname.value)) {
      createElement("#vorname");
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
	

//ende ready