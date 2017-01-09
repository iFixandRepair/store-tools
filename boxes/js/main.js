function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token;
	var profile = googleUser.getBasicProfile();
	var options;
	var sentBoxesTable = null;

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
					"save-box-content.php",
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
				"delete-box-content.php",
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
		trackingNumber = $("#tracking-number").val();
		$.post(
			"close-box.php",
			{
			"XDEBUG_SESSION_START": 12344,
			"id_token": id_token,
			"trackingNumber": trackingNumber
			}, 
			function(){
				$('#close-box-modal').modal('toggle');
				$('a[href="#sentBoxes"]').tab('show');
				$("#new-box-table tbody tr").not(':first').remove();
				loadNewBox();
			}
		);
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
			"box-names-list.php",
			{"id_token": id_token},
			function(data){								
				options  = $.map(
					data.boxNames,
					function(i, n){
						return i.box_name;
					}
				);

				// New Box - Load Current Box
				$.post(
					"open-box.php",
					{"id_token": id_token, "XDEBUG_SESSION_START": 12344},
					function(data, textStatus, jqXHR){			

						$("#store-name").text(data.location + " (" + profile.getEmail() + ")" );

						$.each(data.boxContent, function(i, e){
							if(i == 0)
								fillItem($("#new-box-table tbody tr").first(), e);
							else
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
					var newRow = $("#new-box-table tbody tr").first().clone(true, true);		
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
		if(sentBoxesTable == null)
		$.post("sent-boxes.php",
			{"id_token": id_token},			
			function(data){

				 sentBoxesTable = $('#sent-boxes-table').DataTable(
					{
						"data": data.sentBoxes,
						"columns": [
							{
								"className":      'details-control',
								"orderable":      false,
								"data":           null,
								"defaultContent": '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>'
							},
							{ "data": "ship_date" },
							{ "data": "tracking_number" }
						],
						"order": [[1, 'desc']]
					}					
				);

				$('#sent-boxes-table tbody').on('click', 'td.details-control', function () {
					var tr = $(this).closest('tr');
					var row = sentBoxesTable.row( tr );
			
					if ( row.child.isShown() ) {
						// This row is already open - close it
						row.child.hide();
						$(this).html('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>');
					}
					else {
						// Open this row
						row.child( format(row.data()) ).show();
						$(this).html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>');
					}
				} );

				function format ( d ) {
					// `d` is the original data object for the row
					var detailTable = $('.sent-detail').first().clone();
					$.each(d.box_content, function(i, e){
						var row = $("<tr></tr>");
						row.append($("<td></td>").text(e.box_name));
						row.append($("<td></td>").text(e.recycles_snt));
						row.append($("<td></td>").text(e.recycles_rcv));
						row.append($("<td></td>").text(e.rma_snt));
						row.append($("<td></td>").text(e.rma_rcv));
						row.append($("<td></td>").text(e.doa_snt));
						row.append($("<td></td>").text(e.doa_rcv));
						row.append($("<td></td>").text(e.tecdam_snt));
						row.append($("<td></td>").text(e.tecdam_rcv));
						$("tbody", detailTable).append(row);
					});
					return detailTable.html();
				}
			},
			"json"
		);
	})
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
}