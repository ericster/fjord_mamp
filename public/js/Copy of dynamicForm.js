$(document).ready(function() {

	$('.lead').hover(
			 function() {
				    $( this ).append( $( "<span> ***</span>" ) );
				  }, function() {
				    $( this ).find( "span:last" ).remove();
				  }
			);

	$('.addbtn').on('click', function(e){
		e.preventDefault();
        var currentCount = $('form .appname').length;
//        alert(currentCount);
        var template = $('form > div > fieldset > span').data('template');
//    	document.getElementById("demo").innerHTML= template;
        template = template.replace(/__index__/g, currentCount);
        console.log(template);
//         var str3 = " \</br\>";
        var str3 = "\<div\>";
//         var res = template.concat(str3);
        var res1 = str3.concat(template);
        var res = res1.concat('\</div\>');
        var del = $('#del-button').clone();
        $("#demo").html(del);
        tplate = $(template);
        console.log($(template));
        appn= $(template).find('input.appname');
        console.log(appn);
        $('.demo').append(appn);
//        $('.demo').append('<p><b>Test</b> Paragraph.</p>');


        $('form > div > fieldset').append(res);
        
        app2 =  $('form > div > fieldset').find('.appname');
        fset = $('form > div > fieldset');
        console.log(fset);
        console.log(app2);
    });

  });
