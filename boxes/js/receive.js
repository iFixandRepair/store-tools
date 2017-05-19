<<<<<<< HEAD
<<<<<<< HEAD
function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token;
	var profile = googleUser.getBasicProfile();

	$("#loginContainer").hide();
	$("#newBox").addClass("blinker");
	$("#mainContainer").show();
	initSentBoxesTable(id_token, "admin");
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
=======
=======
>>>>>>> origin/master
function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token;
	var profile = googleUser.getBasicProfile();

	$("#loginContainer").hide();
	$("#newBox").addClass("blinker");
	$("#mainContainer").show();
	initSentBoxesTable(id_token, "admin");
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
<<<<<<< HEAD
>>>>>>> origin/master
=======
>>>>>>> origin/master
}