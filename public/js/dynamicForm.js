$(document).ready(function() {

        // :Dom Event
                // :First form to populate the 2nd form with a temalate file
        $("#exldatasub").submit(function(event){
                
//                alert("AJAX rquest prep<br />") ;
                // prevent default posting of form
            event.preventDefault();
                
            // abort any pending request
//                    if (request) {
//                        request.abort();
//                    }
            // setup some local variables
            /*
            var $form = $(this);
            // let's select and cache all the fields
            var $inputs = $form.find("input");
            var $inputs = $("#exldatasub input[name=uploadTmp]");
            // serialize the data in the form
//            var serializedData = $form.serialize();
            var serializedData = $inputs.serialize();
            console.log(serializedData);
            */


            // let's disable the inputs for the duration of the ajax request
//            $inputs.prop("disabled", true);

            var fd = new FormData();
            fd.append("uploadTmp",  $("#exldatasub input[name=uploadTmp]")[0].files[0]);
//            fd.append("uploadtmp", $("#exldatasub input[name=submit]"));
//         alert("AJAX rquest sending<br />") ;
            // fire off the request to /form.php
            request = $.ajax({
                url: "/album/exlprep3forms",
//                url: "http://localhost:8888/album/exlprep3forms",
//                url: urlform,
                type: "post",
                dataType: "json",
                processData: false,
                data: fd,
//                data: serializedData,
                contentType: false,
                success : function ( testReturn )
                  {
                        console.log("Hooray, it worked!");
                        var jsonString = jsonToString(testReturn);
//                    alert(testReturn) ;
//                        $("#demoeric").html(jsonString);
                        
                        // Iteration to create table row
                        iter_insert_row(testReturn);
//                        iter_insert_row_empty(testReturn);
                        $("#exldatasub input[name=submit]").removeClass("btn-info");

//                    alert("response from AJAX response<br />") ;
//                    alert(testReturn) ;
                  },
                error : function (xhr, err)
                 {
//                    alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
//                    alert("responseText: "+xhr.responseText);
//                    $("#exldatasub input[name=submit]").removeClass("btn-info");
                    $("#exldatasub input[name=submit]").css({ "color": "#333333",
                    		  "background-color": "#b5b5b5",
                    		  "border-color": "#b5b5b5"
                    		});
//                	$("#response").html('A problem has occurred.');
                	// bootstrap popover tooltip.
		            $("#exldatasub input[name='uploadTmp']").popover({title: 'Invalid template', placement: 'top', content: "make sure to use right text format"}); 
                	$("#exldatasub input[name=uploadTmp]").addClass('popover1');
                	$("#exldatasub input[name=uploadTmp]").popover('show');
//                    alert("failed ");
//                    console.log("Oops, it failed!");
//                        alert(data);
//                    alert("error from AJAX response<br />") ;
                	$('body').on('click', function (e) {
                		$("#exldatasub input[name=uploadTmp]").popover('hide');
                	});
                 },
              });
             
//            return false;

            });

//          $("#response").popover({title: 'Twitter Bootstrap Popover', placement: 'top', content: "It's so simple to create a tooltop for my website!"}); 
//          $("#exldatasub input[name='uploadTmp']").popover({title: 'Twitter Bootstrap Popover', placement: 'top', content: "It's so simple to create a tooltop for my website!"}); 

          $("#exldatasub input[name='uploadTmp']").change(function(){
                      $("#exldatasub input[name=submit]").addClass("btn-info");
          });

        // :helper function to change JSON object to string 
        /** 
         * @memberOf jsonToString
         */

        function jsonToString($jsonObject) {
                return JSON.stringify($jsonObject);
        }
        
//        $('#del-button').live("click", function(event){
        $(document).on("click", '#del-button', function(event){
                row = $(this).parent().parent();
                row.remove();
                event.preventDefault();
        });
        
        /** 
         * @memberOf iter_insert_row_empty
         */
        // :helper function to create rows of search item with no data
        function iter_insert_row_empty(arr) {
                var p = arr;
                for (var key in p) {
                          if (p.hasOwnProperty(key)) {
//                                    alert(key + " -> " + p[key]);
                                  insert_row_empty();
                          }
                        }

        }
    
            // :helper function to create rows of search item with a JSON data
        // 
        /** 
         * @memberOf iter_insert_row
         */
        function iter_insert_row(arr) {
                    var p = arr;
                    var currentCount = 0;
                    for (var key in p) {
                              if (p.hasOwnProperty(key)) {
        //                                    alert(key + " -> " + p[key]);
                                            app_name = key;
                                            search_term = p[key];
                                                    insert_row( currentCount, app_name, search_term);
//                                            insert_row_empty();
                              }
                            currentCount = currentCount + 1;
                    }
            }
                
            // :Dome Event
                // :jUqeury UI test to check ui file is included
        //    $(".table").tablesorter();
        
                // Dom Event
            $('.lead').hover(
                             function() {
                                        $( this ).append( $( "<span> ***</span>" ) );
                                      }, function() {
                                        $( this ).find( "span:last" ).remove();
                                      }
                            );
            // :Dom Event
            // :test adding button to add a new row
            $('.addbtn').on('click', function(e){
                e.preventDefault();
                insert_row_empty();
            
                // Sample from ZF2 
                app2 =  $('form > div > fieldset').find('.appname');
                fset = $('form > div > fieldset');
            });

    // :helper function adding the first row: not used 

        /**
        * @memberOf insert_row_first
        */
    function insert_row_first( currentCount, app_name, search_term) {
        var template = '<input name="searchTerm[__index__][appName]" required="required" class="appname" type="text" value="__app_name__"> \
                <input name="searchTerm[__index__][regexPattern]" required="required" class="appregex" type="text" value="__search_term__">';
        template = template.replace(/__index__/g, currentCount);
//        var del = $('#del-button').clone();
            appn.val(app_name);
            appr.val(search_term);
    }

    // :helper function to add a row 
    /**
     * @memberOf insert_row
     */
    function insert_row( currentCount, app_name, search_term) {

        if (currentCount == 0 )  {
                $( "input[name*='appName']" ).val( app_name );
                $( "input[name*='regexPattern']" ).val( search_term);
        }

        var template = '<input name="searchTerm[__index__][appName]" required="required" class="appname" type="text" value="__app_name__"> \
                <input name="searchTerm[__index__][regexPattern]" required="required" class="appregex" type="text" value="__search_term__">';
        if (currentCount != 0 )  {
                template = template.replace(/__index__/g, currentCount);
                template = template.replace(/__app_name__/g, app_name);
                template = template.replace(/__search_term__/g, search_term);
        }
//        console.log(template);
//        var del = $('#del-button').clone();
        var del = '<button type="button" class="btn btn-danger btn-sm" id="del-button"> \
        <span class="glyphicon glyphicon-trash"></span></button> </td> </tr>';
        
        var tplate = $(template);
        var parsed = $('<div/>').append(tplate);
        var appn= parsed.find('input.appname');
        var appr= parsed.find('input.appregex');


        appn.addClass('input-normal');
        appr.addClass('input-xxlarge');
        var appn_td = $('<td></td>').append(appn);
        var appr_td = $('<td></td>').append(appr);
        var del_td = $('<td></td>').append(del); 
        var app_tr = $('<tr></tr>').append(appn_td).append(appr_td).append(del_td);
//        console.log(parsed);
//        console.log(appn_td);
//        console.log(appr);

        // Appending to target tag

        if (currentCount != 0 )  {
                $('.collectionTable tr:last').after(app_tr);
        }
        
        }

    // helper function to add an empty row
    /**
     * @memberOf insert_row_empty
     */
    function insert_row_empty() {
        var currentCount = $('form .appname').length;
//                alert(currentCount);
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
//        var del = $('#del-button').clone();
        var del = '<button type="button" class="btn btn-danger btn-sm" id="del-button"> \
        <span class="glyphicon glyphicon-trash"></span></button> </td> </tr>';
        
        // Test HTML tag
        var row_empty = '<tr class="nameRegex"> <td> col1 </td> <td> col2 </td> <td>  \
                <button type="button" class="btn btn-danger btn-sm" id="del-button"> \
                <span class="glyphicon glyphicon-trash"></span> </button> </td> </tr>';
        var div_add = '<li> Hello World </li>'
        var div_add2 = '<li> Hello World2 </li>'
        var div_add3 = '<p> Hello World2 </p>'
       

//        $("#demoeric").html(del);
        var tplate = $(template);
//        console.log($(template));
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
//        console.log(parsed);
//        console.log(appn_td);
//        console.log(appr);


        // Appending to target tag
        $('.collectionTable tr:last').after(app_tr);
        
        // Testing a table
        $('#myTable tr:last').after(row_empty);
        }


  });