$(document).ready(function() {

	$('.lead').hover(
			 function() {
				    $( this ).append( $( "<span> ***</span>" ) );
				  }, function() {
				    $( this ).find( "span:last" ).remove();
				  }
			);
	
	// Tabs for options to switch from tempalte to manual input
	$( "#tabs" ).tabs();

	$('.addbtn').on('click', function(e){
		e.preventDefault();
        var currentCount = $('form .appname').length;
//        alert(currentCount);
//        var template = $('form > div > fieldset > span').data('template');
//        var template = $('form > div > fieldset > span').data('template');
//    	document.getElementById("demo").innerHTML= template;
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

        // Appending to test target tag
//        $('#demo').append(div_add3);

//        appn.appendTo($('#demoeric'));
//        appr.appendTo($('#demoeric'));
//        $('.demo').append(appn);
//        $('.demo').append(row_empty);
//        console.log($('#demoeric li:last'));
//        $('#demoeric li:last').after(div_add2);

//        $('.demo').append('<p><b>Test</b> Paragraph.</p>');


//        $('form > div > fieldset').append(res);
//        $('.collectionTable tr:last').after(row_empty);

        // Appending to target tag
        $('.collectionTable tr:last').after(app_tr);
        
        // Testing a table
        $('#myTable tr:last').after(row_empty);
        
        // Sample from ZF2 
        app2 =  $('form > div > fieldset').find('.appname');
        fset = $('form > div > fieldset');
//        console.log(fset);
//        console.log(app2);
//        var approw = $('.nameRegex').clone();
    });

  });
