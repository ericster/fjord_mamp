$(document).ready(function() {

	// Dom Event
	$("#exldatasub").submit(function(event){
		
//	        alert("AJAX rquest prep<br />") ;
		// prevent default posting of form
	    event.preventDefault();
		
	    // abort any pending request
//		    if (request) {
//		        request.abort();
//		    }
		/*
	    // setup some local variables
	    var $form = $(this);
	    // let's select and cache all the fields
	    var $inputs = $form.find("input, select, button, textarea");
	    // serialize the data in the form
	    var serializedData = $form.serialize();
	    */

	    // let's disable the inputs for the duration of the ajax request
//	    $inputs.prop("disabled", true);

         alert("AJAX rquest sending<br />") ;
	    // fire off the request to /form.php
	    request = $.ajax({
	        url: "/album/exlprep3forms",
	        type: "post",
	        dataType: "json",
//	        data: serializedData,
	        success : function ( testReturn )
	          {
	        	var p = testReturn;
	        	for (var key in p) {
	        		  if (p.hasOwnProperty(key)) {
//	        		    alert(key + " -> " + p[key]);
	        			  insert_row_empty();
	        		  }
	        		}
	        	console.log("Hooray, it worked!");
	        	var jsonString = jsonToString(testReturn);
	            alert(jsonString) ;
		        $("#demoeric").html(jsonString);
//	            alert("response from AJAX response<br />") ;
//	            alert(testReturn) ;
	          },
	        error : function (xhr, err)
	         {
	            alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
	            alert("responseText: "+xhr.responseText);
	        	console.log("Oops, it failed!");
//	        	alert(data);
//	            alert("error from AJAX response<br />") ;
	         },
	      });

	    });

	function jsonToString($jsonObject) {
		return JSON.stringify($jsonObject);
	}
	
	// Working On:
    function iter_insert_row_empty($arr) {
    	var $p = $arr;
    	for (var key in $p) {
    		  if (p.hasOwnProperty(key)) {
//	        		    alert(key + " -> " + p[key]);
    			  insert_row_empty();
    		  }
    	}
    }
	
	// Dom Event
    $('.lead').hover(
                     function() {
                                $( this ).append( $( "<span> ***</span>" ) );
                              }, function() {
                                $( this ).find( "span:last" ).remove();
                              }
                    );
    // Dom Event
    $('.addbtn').on('click', function(e){
        e.preventDefault();
        insert_row_empty();
    
        // Sample from ZF2 
        app2 =  $('form > div > fieldset').find('.appname');
        fset = $('form > div > fieldset');
//        console.log(fset);
//        console.log(app2);
//        var approw = $('.nameRegex').clone();
    });

    // Local Function
    function insert_row_empty() {
        var currentCount = $('form .appname').length;
//	        alert(currentCount);
//        var template = $('form > div > fieldset > span').data('template');
//        var template = $('form > div > fieldset > span').data('template');
//            document.getElementById("demo").innerHTML= template;
        var template = '<input name="searchTerm[__index__][appName]" required="required" class="appname" type="text" value=""> \
                <input name="searchTerm[__index__][regexPattern]" required="required" class="appregex" type="text" value="">';
        var rowCount = $('.collectionTable tr').length;
        template = template.replace(/__index__/g, currentCount);
//        console.log(template);
//         var str3 = " \</br\>";
        var str3 = "\<div\>";
//         var res = template.concat(str3);
        var res1 = str3.concat(template);
        var res = res1.concat('\</div\>');
        var del = $('#del-button').clone();
        
        // Test HTML tag
        var row_empty = '<tr class="nameRegex"> <td> col1 </td> <td> col2 </td> <td>  \
                <button type="button" class="btn btn-danger btn-sm" id="del-button"> \
                <span class="glyphicon glyphicon-trash"></span> Delete</button> </td> </tr>';
        var div_add = '<li> Hello World </li>'
        var div_add2 = '<li> Hello World2 </li>'
        var div_add3 = '<p> Hello World2 </p>'
       

//        $("#demoeric").html(del);
        var tplate = $(template);
        console.log($(template));
        var parsed = $('<div/>').append(tplate);
//        parsed.find(".class0")
        var appn= parsed.find('input.appname');
        var appr= parsed.find('input.appregex');
        appn.addClass('input-normal')
        appr.addClass('input-xxlarge')
        var appn_td = $('<td></td>').append(appn);
        var appr_td = $('<td></td>').append(appr);
        var del_td = $('<td></td>').append(del); 
        var app_tr = $('<tr></tr>').append(appn_td).append(appr_td).append(del_td);
        console.log(parsed);
        console.log(appn_td);
        console.log(appr);


        // Appending to target tag
        $('.collectionTable tr:last').after(app_tr);
        
        // Testing a table
        $('#myTable tr:last').after(row_empty);
	}


  });