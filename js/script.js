let action = "";

function appendRows(result, result_form){
	var str = '<thead><tr id = "trTable"><th score="col">id</th><th score="col">Квартира</th><th score="col">Дата</th><th score="col">Сумма</th><th score="col">Функция</th></tr></thead>',
	body = "";
	for (var i = 0, l = result["id_book"].length;i < l;i++){
		body+=`<tr><th score="col">${result["id_book"][`${i}`]}</th><th score="col">${result["name"][`${i}`]}</th><th score="col">${result["author"][`${i}`]}</th><th score="col">${result["date"][`${i}`]}</th><th score="col">${result["key_words"][`${i}`]}</th><th score="col">${result["description"][`${i}`]}</th></tr>`;
	};
	$('#'+result_form).html(body);}

function handlerServerResult(result, result_form)
{
	if ("error" in result){
		alert(`Php: Error, ${result[`error`]}`);
	}
	else{
		if ("action" in result){
			switch(result["action"]){
				case "show_books":
					$("#tableBody").text("");
					appendRows(result, result_form);
					break;
				case "add":
					//alert(result["success"])
					break;
				case "delete":
					//alert("success");
			}
		}
		else{
			alert("Error. Response do not have key action.");
		}
	}}
function sendAjaxRequest(url, type, action, result_form, ajax_form){
	$.ajax({
		url: url,
		type: type,
		dataType: "html",
		data: (ajax_form!=undefined)? $("#"+ajax_form).serialize()+`&action=${action}`:`action=${action}`,
		success: function(response){
			var result = $.parseJSON(response);
			handlerServerResult(result, result_form);
		},
		error: function(response){
			alert("sendAjaxRequest: Error");
		}
	});}

function sendAjaxRequestRangeId(url, table){
	$.ajax({
		url: url,
		type: "GET",
		data: {action: "rangeId", table: table},
		success: function(response){
			var result =  $.parseJSON(response);
			$("#from").val(result['min']!=null ?result['min']:0);
			$("#to").val(result['max']!=null ?result['max']:0);
		},
		error: function(response){
			alert("sendAjaxRequestRangeId: Error");
		}
	});}
function validate(form){
	array = $("#"+form).serializeArray();
}

//функция при срабатывании клика на кнопку filterButton
function BlockButtonClick(id){
	var element = $('#'+id)[0];
	element.style.display = (element.style.display == "none" ? "block" : "none");}

$(document).ready(function(){
	$("#filtersParam")[0].style.display = "none";
	$("#findParam")[0].style.display = "none";
	$("#change")[0].style.display = "none";

	sendAjaxRequestRangeId("scripts/handler.php", "books");
	sendAjaxRequest("scripts/handler.php", "GET", "show_books", "tableBody")

	$('#filterButton').click(function(){
		BlockButtonClick('filtersParam');
		$('#apply').click(function(){prompt("apply")});
	});
	$('#findButton').click(function(){
		BlockButtonClick('findParam');
		$('#find').click(function(){prompt("find")});
	});
	$('#add').click(function(){
		BlockButtonClick('change');
		$("#add_mod")[0].style.display = "block";
		action = "add";
	});
	$('#delete').click(function(){
		BlockButtonClick('change');
		$("#add_mod")[0].style.display = "none";
		action = "delete";
	});
	$('#edit').click(function(){

	});
	$('#send').click(function(){sendAjaxRequest("scripts/handler.php", "POST", action, "", "ajax_change")});
});
