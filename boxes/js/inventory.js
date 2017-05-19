function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token;
	var profile = googleUser.getBasicProfile();

	$("#loginContainer").hide();
	$("#newBox").addClass("blinker");
	$("#mainContainer").show();

	// File Upload
	$("#product-detail-upload input[type=file]").change(function(e){
		e.stopPropagation();
    	e.preventDefault();
		var data = new FormData();
		//data.append("XDEBUG_SESSION_START", 12344);
		data.append('productDetailFile', this.files[0]);
		data.append('XDEBUG_SESSION_START', 12345);
		var inputTrigger = $(this);
		var parentUl = inputTrigger.closest("ul");
<<<<<<< HEAD
<<<<<<< HEAD
		parentUl.append("<li><p class=\"navbar-text blinkerText\">Loading...</p><li>");
=======
		parentUl.append("<li>Loading...<li>");
>>>>>>> origin/master
=======
		parentUl.append("<li>Loading...<li>");
>>>>>>> origin/master
		$.ajax(
			{
				url:"../services/upload-product-detail.php",
				type: "POST",
				data: data,
				dataType: "text",
				cache: false,
				encode: true,
				processData: false,
        		contentType: false
			}			
		).done(function(data){
			inputTrigger.val('');
			parentUl.children("li:gt(0)").remove();
		}).fail(function(jqXHR, textStatus, errorThrown) {
			alert( errorThrown );
		});
	});

<<<<<<< HEAD
<<<<<<< HEAD
	initSentBoxesTable(id_token, "admin");
=======
=======
>>>>>>> origin/master
	// Sent Boxes	
	initSentBoxesTable(id_token, "admin");
	$('a[href="#sentBoxes"]').on('shown.bs.tab', function (e) {
		initSentBoxesTable(id_token, "admin");
	});
	//$('a[href="#sentBoxes"]').tab('show');
<<<<<<< HEAD
>>>>>>> origin/master
=======
>>>>>>> origin/master
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
}