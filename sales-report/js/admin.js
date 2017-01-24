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

	// File Upload
	$("#product-detail-upload").submit(function(e){
		e.stopPropagation();
    	e.preventDefault();
		var data = new FormData();
		//data.append("XDEBUG_SESSION_START", 12344);
		var files = $('input[type=file]', this)[0].files;
		data.append('productDetailFile', files[0]);
		$.ajax(
			{
				url:"upload-product-detail.php",
				type: "POST",
				data: data,
				dataType: "text",
				cache: false,
				encode: true,
				processData: false,
        		contentType: false
			}
		).fail(function(jqXHR, textStatus, errorThrown) {
			alert( errorThrown );
		});
	});

	// Sent Boxes
	if(sentBoxesTable == null)
		$.post("admin-sent-boxes.php",
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
							{ "data": "location" },
							{ "data": "ship_date" },
							{ "data": "tracking_number" }
						],
						"order": [[2, 'desc']]
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
						row.append($("<td></td>").append($("<input>").val(e.recycles_rcv)));
						row.append($("<td></td>").text(e.rma_snt));
						row.append($("<td></td>").append($("<input>").val(e.rma_rcv)));
						row.append($("<td></td>").text(e.doa_snt));
						row.append($("<td></td>").append($("<input>").val(e.doa_rcv)));
						row.append($("<td></td>").text(e.tecdam_snt));
						row.append($("<td></td>").append($("<input>").val(e.tecdam_rcv)));
						$("tbody", detailTable).append(row);
					});
					return detailTable.html();
				}
			},
			"json"
		);
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
}