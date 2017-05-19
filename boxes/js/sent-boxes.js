function initSentBoxesTable(id_token, sessionType){
    switch(sessionType){
        case "store":
            sentBoxesSource = "services/sent-boxes.php";
            htmlDetailSource = "services/sent-boxes.html?v1.0";
        break;
        default:
            sentBoxesSource = "../services/admin-sent-boxes.php";
            htmlDetailSource = "../services/admin-sent-boxes.html?v1.7";
        break;
    }

    var minusButtonHTML = '<button class="btn btn-xs btn-del-item"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>';

    //--Load Boxes.
    $.post(sentBoxesSource,
        {"id_token": id_token},			
        function(data){
            var boxNamesList = $.map(
					data.boxNamesList,
					function(i, n){
						return i.box_name;
					}
				);
            var detailNameList;
            $.get(htmlDetailSource, function(html){
                
                var columns = [];
                columns.push(
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>'
                    }
                );
                
                if(sessionType != "store")
                    columns.push({ "data": "location" });
                
                columns.push({ "data": "ship_date" });
                
                if(sessionType != "store")
                    columns.push({ 
                        "data": "tracking_number",
                        "render": function(data, type, row){                            
                            return '<input type="text" data-store-id="' + row[0] + '" data-box-id="' + row[0] + '" class="track_num_input" value="' + data + '">';
                        }
                    });
                else
                    columns.push({ "data": "tracking_number" });

                //--Datatables Call
                var sentBoxesTable = $('#sent-boxes-table').DataTable(
                    {
                        "data": data.sentBoxes,
                        "columns": columns,
                        "pageLength": 100,
                        "order": [[2, 'desc']]
                    }					
                );
                
                
                $('a[href="#sentBoxes"]').on('hide.bs.tab', function (e) {
                    sentBoxesTable.destroy();
                    $( "#sent-boxes-table tbody").empty();
                    $( "#sentBoxes>.panel-success:visible" ).collapse('hide');
                    $(this).off('hide.bs.tab');
                });
                //--End - Datatables Call.


                if(sessionType != "store" && $("#openBoxes").length > 0){
                    var openBoxesTable = $('#open-boxes-table').DataTable(
                        {
                            "data": data.openBoxes,
                            "columns": [
                                {
                                    "className":      'details-control',
                                    "orderable":      false,
                                    "data":           null,
                                    "defaultContent": '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>'
                                },
                                { "data": "location" }]
                        }					
                    );
                    
                    $('a[href="#sentBoxes"]').on('hide.bs.tab', function (e) {
                        openBoxesTable.destroy();
                        $(this).off('hide.bs.tab');
                    });
                    //--Detail Row Behavior.
                    $('#open-boxes-table tbody').on('click', 'td.details-control', function () {
                        var tr = $(this).closest('tr');
                        var row = openBoxesTable.row( tr );
                        if ( row.child.isShown() ) {
                            // This row is already open - close it
                            row.child.hide();
                            $(this).html('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>');
                        }
                        else {
                            // Open this row
                            if(row.child() === undefined)
                                row.child( openBoxDetail(row.data()) );
                            row.child.show();
                        }
                    });
                }

                //--Detail Row Behavior.
                $('#sent-boxes-table tbody').on('click', 'td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = sentBoxesTable.row( tr );
                    detailNameList = boxNamesList.slice(0);

                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        $(this).html('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>');
                    }
                    else {
                        // Open this row
                        if(row.child() === undefined){
                            row.child( formatDetailRow(row.data()) );
                            //Admin tools
                            if(sessionType == "admin"){
                                
                                lightZeros(row.child());

                                //Delete buttons
                                $("tbody>tr", row.child()).each(function(){
                                    toggleDeleteButton($(this));
                                });

                                // Box Detail- Individual Text Inputs Behavior.                        
                                $(".quantity", row.child()).on( 
                                    "keypress change",
                                    function(e){
                                        qtyChangeHandler($(this), e);
                                    }
                                );
                                // End - Box Detail- Individual Text Inputs Behavior.

                                // Box Detail - Add Item
                                $(".btn-add-item", row.child()).click(function(e){
                                    var boxId = $("tbody tr", row.child()).last().data("box-id");
                                    var emptyBoxItem = {
                                        box_name: "autocomplete",
                                        box_id: boxId,
                                        recycles_exp: 0,
                                        recycles_rcv: 0,
                                        recycles_snt: 0,
                                        rma_rcv: 0,
                                        rma_snt: 0,
                                        doa_rcv: 0,
                                        doa_snt: 0,
                                        ship_date: 0,
                                        ship_method: 0,
                                        store_id: 0,
                                        tecdam_rcv: 0,
                                        tecdam_snt: 0
                                    };
                                    addDetailRow(emptyBoxItem, row.child());
                                    $("input.box-name-input", row.child()).autocomplete({source: detailNameList});
                                    var newTr = $("tr", row.child().find("tbody")).last();
                                    toggleDeleteButton(newTr);
                                    $(".quantity", newTr).on( 
                                        "keypress change",
                                        function(e){qtyChangeHandler($(this), e);}
                                    );
                                    var n = $("tbody", row.child()).scrollHeight;
                                    $("tbody", row.child()).animate({ scrollTop: n });
                                    $("input.box-name-input", newTr).focus()
                                });
                                // End - Box Detail - Add Item.
                            }// End - Admin Tools
                        }
                        row.child.show();
                        $(this).html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>');
                       
                        
                        
                        // Qty Inputs Behavior
                        function qtyChangeHandler(inputField, e){
                            if(e.type !='keypress' || e.which == 13){
                                var trItem = inputField.closest("tr");
                                var boxId = trItem.data("box-id");                                    
                                var boxName = getBoxName(trItem);
                                var field = inputField.attr("name");
                                var value = inputField.val();
                                var trigger = inputField;                                    
                                trigger.addClass("blinker");
                                $.post(
                                    "../services/admin-save-box-content.php",
                                    {
                                    "XDEBUG_SESSION_START": 12344,					
                                    "id_token": id_token,
                                    "boxId": boxId,
                                    "boxName": boxName,
                                    "field": field,
                                    "value": value
                                    },
                                    function(){
                                        var tdItem = trigger.closest("td");
                                        var tableItem = trigger.closest("table");
                                        var tdIndex = $("td", trItem).index(tdItem);                                            
                                        var total = 0;
                                        $("td[data-subtr]", trItem).text(function(){
                                            return calcFormula($(this).attr("data-subtr"), trItem);
                                        });
                                        setRowColor(trItem);
                                        $("tfoot th:gt(1)").text(0);
                                        $("tbody>tr", tableItem).each(function(){
                                            $("td:gt(1)", this).each(function(i, v){
                                                $("tfoot th:nth-child(" + (i+3) + ")").text(function(){
                                                    var toAdd = ($("input", v).length > 0) ? $("input", v).val() : $(v).text();
                                                    return Number($(this).text()) + Number(toAdd);
                                                });
                                            });
                                        });                                        
                                        trigger.removeClass("blinker");
                                    }
                                );
                            }
                        }// End - Qty Inputs Behavior.
                    }
                });// End - Detail Row Behavior.                

                $(".track_num_input").on("keypress change", function(e){
                    if(e.type !='keypress' || e.which == 13){
                        var inputField = $(this)
                        var trItem = inputField.closest("tr");
                        var boxId = inputField.data("box-id");
                        var trackingNumber = inputField.val();
                        inputField.addClass("blinker");
                        $.post(
                            "../services/admin-save-tracking-number.php",
                            {
                            "XDEBUG_SESSION_START": 12344,					
                            "id_token": id_token,
                            "boxId": boxId,                            
                            "trackingNumber": trackingNumber
                            },
                            function(){
                                inputField.removeClass("blinker");
                            }
                        );
                    }
                 });

                // Detail Row Format.
                var detailRowCells = [
                        {dbFieldName: "recycles_snt", storeBehav: "default", adminBehav: "default"},
                        {dbFieldName: "recycles_rcv", storeBehav: "default", adminBehav: "edit"},
                        {dbFieldName: "3-4", storeBehav: "hide", adminBehav: "subtr"},
                        {dbFieldName: "recycles_exp", storeBehav: "hide", adminBehav: "default"},
                        {dbFieldName: "6-4", storeBehav: "hide", adminBehav: "subtr"},
                        {dbFieldName: "rma_snt", storeBehav: "default", adminBehav: "default"},
                        {dbFieldName: "rma_rcv", storeBehav: "default", adminBehav: "edit"}, 
                        {dbFieldName: "doa_snt", storeBehav: "default", adminBehav: "default"},
                        {dbFieldName: "doa_rcv", storeBehav: "default", adminBehav: "edit"},
                        {dbFieldName: "tecdam_snt", storeBehav: "default", adminBehav: "default"},
                        {dbFieldName: "tecdam_rcv", storeBehav: "default", adminBehav: "edit"}
                    ]

                function formatDetailRow ( d ) {
                    var detailTable = $(html);                    

                    $.each(d.box_content, function(i, e){
                        var row = addDetailRow(e, detailTable)
                        $("td", row).each(function(j, v){
                            var startColumn = (sessionType != "store") ? 2 : 1
                            if(j >= startColumn)
                                $("tfoot tr th:nth-child("+(j+1)+")", detailTable).text(
                                    function(){                                       
                                        toAdd = ($("input", v).length > 0) ? $("input", v).val() : $(v).text();
                                        return Number($(this).text()) + Number(toAdd);              
                                    }
                                );
                        });
                    });
                    return detailTable.html();
                }

                function openBoxDetail ( d ) {
                    var detailTable = $("<table></table>");
                    detailTable.append("<thead><th>Box Name</th><th>Recycles</th><th>RMA</th><th>DOA</th><th>Tech Damage</th></thead>");

                    $.each(d.box_content, function(i, e){
                        detailTable.append(
                            $("<tr></tr>")
                            .append("<td>" + e.box_name + "</td>")
                            .append("<td>" + e.recycles_snt + "</td>")
                            .append("<td>" + e.rma_snt + "</td>")
                            .append("<td>" + e.doa_snt + "</td>")
                            .append("<td>" + e.tecdam_snt + "</td>")
                        );
                    });
                    return detailTable.html();
                }

                function addDetailRow(boxItem, detailTable){
                    var detailRow = $("<tr data-box-id=\"" + boxItem.box_id + "\"></tr>");
                    
                    if(sessionType == "admin")
                        detailRow.append($("<td></td>"));
                    
                    if(boxItem.box_name == "autocomplete")
                        detailRow.append($("<td></td>").append($('<input type="text" class="box-name-input form-control">')).addClass("box-name"));
                    else{
                        detailRow.append($("<td></td>").text(boxItem.box_name).addClass("box-name"));
						detailNameList.splice(detailNameList.indexOf(boxItem.box_name), 1);
                    }
                    
                    $(detailRowCells).each(function(n, cell){
                        addDetailCell(detailRow, cell, boxItem);
                    });
                     setRowColor(detailRow);
                    $("tbody", detailTable).append(detailRow);
                    // New Box - Name Selectors Behavior.
                    $(".box-name-input").on( 
                        "keypress focusout",
                        function(e){
                            var valIndex = detailNameList.indexOf($(this).val());
                            if((e.type !='keypress' || e.which == 13) && valIndex != -1){
                                detailNameList.splice(valIndex, 1);
                                $(this).prop( "disabled", true );
                            }
                        }
                    );
                    return detailRow;
                }

                function addDetailCell(row, cell, boxItem){
                    var behavior = (sessionType == "admin") ? cell.adminBehav : cell.storeBehav;                    
                    if(behavior != "hide"){
                        switch(behavior){
                            case "edit":
                                row.append($("<td></td>").append(
                                    $("<input>")
                                    .attr("value", boxItem[cell.dbFieldName])
                                    .addClass("quantity")
                                    .addClass("form-control")
                                    .attr("name", cell.dbFieldName)
                                    ));
                                break;
                            case "subtr":
                                var result = calcFormula(cell.dbFieldName, row);
                                row.append($("<td></td>").attr("data-subtr", cell.dbFieldName).text( result));
                                break;
                            default:
                                row.append($("<td></td>").text(boxItem[cell.dbFieldName]));
                                break;
                        }
                    }                    
                }

                function calcFormula(formula, row){
                    var tdIds = formula.split("-");
                    var operands = [];
                    $(tdIds).each(function(i, tdId){
                        var tdObj = $("td:nth-child("+tdId+")", row);                                    
                        var inputs = $("input", tdObj);
                        operands[i] = ( inputs.length > 0 ) ? $(inputs[0]).val() : tdObj.text();
                    });
                    return Number(operands[0]) - Number(operands[1]);
                }

                function getBoxName(trItem){
                    var boxName = trItem.find(".box-name-input").val();
                    if(boxName === undefined)
                        boxName = trItem.find(".box-name").text();
                    return boxName;
                }

                function toggleDeleteButton(row){
                    var blockedTds = $("td:gt(1):not([data-subtr]):not(:has(>input))", row)
                        .filter(function(){
                            return Number($(this).text()) > 0;
                        });
                    if(blockedTds.length == 0){
                        $("td", row).first().append(
                            $(minusButtonHTML).click(function(){
                                var boxId = row.data("box-id");
                                var boxName = getBoxName(row);                            
                                $.post(
                                    "../services/admin-delete-box-content.php",
                                    {
                                    "XDEBUG_SESSION_START": 12344,					
                                    "id_token": id_token,
                                    "boxId": boxId,
                                    "boxName": boxName
                                    },
                                    function(){
                                        detailNameList.splice(0,0,boxName);
                                        row.remove();
                                    }
                                );
                            })
                        );
                    }
                }

                function setRowColor(trItem){
                    var difs = $("td[data-subtr]", trItem).filter(function(index){
                            return Number($(this).text()) != 0;
                        }).length;
                    if(difs == 1)
                        $("td:nth-child(2)", trItem).nextUntil("td:nth-child(8)", trItem).css("background-color", "lightgoldenrodyellow");
                    else if(difs >= 2)
                        $("td:nth-child(2)", trItem).nextUntil("td:nth-child(8)", trItem).css("background-color", "lightcoral");
                    lightZeros(trItem);
                }

                function lightZeros(context){
                    $("td", context).filter(function(){return $(this).text() === "0"}).css("color", "lightgray");
                    $("input", context).filter(function(){return $(this).val() === "0"}).css("color", "lightgray");
                    $("td", context).filter(function(){return $(this).text() != "0"}).css("color", "inherit");
                    $("input", context).filter(function(){return $(this).val() != "0"}).css("color", "inherit");
                }
            });
            //-- End - Load Table.
        },
        "json"
    );
    //-- End - Load Boxes.

}


