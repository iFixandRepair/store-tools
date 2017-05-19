function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token;
	var profile = googleUser.getBasicProfile();
	var options;

	$("#loginContainer").hide();
	$("#newBox").addClass("blinker");
	$("#mainContainer").show();

	// New Box - Individual Text Inputs Behavior.  
	$(".quantity").on( 
		"keypress change",
		function(e){
			if(e.type !='keypress' || e.which == 13){
				var parent = $(this).parents("tr");
				var boxName = parent.find(".box-name").val();
				var field = $(this).data("field");
				var value = $(this).val();
				var trigger = $(this); 
				trigger.addClass("blinker");
				$.post(
					"services/save-box-content.php",
					{
					"XDEBUG_SESSION_START": 12344,
					"id_token": id_token,
					"boxName": boxName,
					"field": field,
					"value": value
					}, 
					function(){trigger.removeClass("blinker")}
				);
			}
		}
	);

	// New Box - Delete Row Button Behavior.
	$(".btn-del-item").click(
		function(){
			var parent = $(this).parents("tr");
			var boxName = parent.find(".box-name").val();
			parent.addClass("blinker");
			$.post(
				"services/delete-box-content.php",
				{
				"XDEBUG_SESSION_START": 12344,
				"id_token": id_token,
				"boxName": boxName
				}, 
				function(){
					parent.remove();
					options.splice(0,0,boxName);
				}
			);			
		}
	);

	// New Box - Close Button Behavior.
	$("#close-box-btn").click(function(){
		if(confirm("Please confirm that you want to save this box.")){
			$.post(
				"services/close-box.php",
				{
				"XDEBUG_SESSION_START": 12344,
				"id_token": id_token,
				}, 
				function(){				
					$('a[href="#sentBoxes"]').tab('show');
					$('#sentBoxes>.panel-success').collapse();
					$("#new-box-table tbody tr").remove();
					loadNewBox();
				}
			);
		}
	});

	// New Box - Name Selectors Behavior.
	$(".box-name").on( 
		"keypress focusout",
		function(e){
			var valIndex = options.indexOf($(this).val());
			if((e.type !='keypress' || e.which == 13) && valIndex != -1){
				options.splice(valIndex, 1);
				$(this).prop( "disabled", true );
			}
		}
	);

	loadNewBox();

	// New Box - Automcomplete values preload.
	function loadNewBox(){
		$.post(	
			"services/box-names-list.php",
			{"id_token": id_token, "XDEBUG_SESSION_START": 12344},
			function(data){
				options  = $.map(
					data.boxNames,
					function(i, n){
						return i.box_name;
					}
				);

				// New Box - Load Current Box
				$.post(
					"services/open-box.php",
					{"id_token": id_token, "XDEBUG_SESSION_START": 12344},
					function(data, textStatus, jqXHR){			

						$("#store-name").text(data.location + " (" + profile.getEmail() + ")" );

						$.each(data.boxContent, function(i, e){
								addItem(e);
						})
						$("#newBox").removeClass("blinker");
						$("#new-box-table").show();
					},
					"json"
				);

				$(".btn-add-item").click(
					function(e){
						addItem(null);
						$("#new-box-table tbody tr .box-name").last().autocomplete({source: options});
					}
				);

				function addItem(boxContentRow){
					var newRow = $("#new-box-reference-table tr").first().clone(true, true);		
					fillItem(newRow, boxContentRow);		
					$("#new-box-table tbody").append(newRow);				
				}

				function fillItem(row2Fill, boxContentRow){
					if(boxContentRow == null){
						$(".box-name", row2Fill).val("").prop( "disabled", false );
						$(".quantity", row2Fill).val(0);	
					}else{
						$(".box-name", row2Fill).val(boxContentRow.box_name);					
						var valIndex = options.indexOf(boxContentRow.box_name);
						options.splice(valIndex, 1);
						$(".box-name", row2Fill).prop( "disabled", true );
						$("input[data-field=recycles_snt]", row2Fill).val(boxContentRow.recycles_snt);
						$("input[data-field=recycles_rcv]", row2Fill).val(boxContentRow.recycles_rcv);
						$("input[data-field=rma_snt]", row2Fill).val(boxContentRow.rma_snt);
						$("input[data-field=rma_rcv]", row2Fill).val(boxContentRow.rma_rcv);
						$("input[data-field=doa_snt]", row2Fill).val(boxContentRow.doa_snt);
						$("input[data-field=doa_rcv]", row2Fill).val(boxContentRow.doa_rcv);
						$("input[data-field=tecdam_snt]", row2Fill).val(boxContentRow.tecdam_snt);
						$("input[data-field=tecdam_rcv]", row2Fill).val(boxContentRow.tecdam_rcv);
					}
				}
			},
			"json"
		);
	}

	// Sent Boxes
	$('a[href="#sentBoxes"]').on('shown.bs.tab', function (e) {
			initSentBoxesTable(id_token, "store");
	});
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
}