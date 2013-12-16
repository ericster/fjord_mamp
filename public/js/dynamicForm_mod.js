$(document).ready(function() {

        // :Dom Event
        /*
        * 2nd main form to populate the 2nd form with a temalate file
        */
	
		$("#exldata1").submit(function(event){
            event.preventDefault();
			var queryarray = $("#exldata").serializeArray();
			console.log(queryarray);
            $('#result').html(queryarray);			
		});
		
		function ConvertFormToJSON(form){
		    var array = jQuery(form).serializeArray();
		    var json = {};
		    
		    jQuery.each(array, function() {
		        json[this.name] = this.value || '';
		    });
		    
		    return json;
		}

        $("#exldata").submit(function(event){
                
//                alert("AJAX rquest prep<br />") ;
                // prevent default posting of form
            event.preventDefault();

			var serializedData = $("#exldata").serializeArray();
            $('#result').html(serializedData);			
                
            var fd = new FormData();
//            fd.append("uploadExl",  $("#exldata input[name=uploadExl]")[0].files[0]);
            fd.append("uploadExl",  $("#exldatasub input[name=uploadExl]")[0].files[0]);
            fd.append("taskName",  $("#exldata input[name=taskName]").val());
            jQuery.each(serializedData, function() {
		        fd.append(this.name, this.value);
		    });
            
            
//            fd.append("uploadtmp", $("#exldatasub input[name=submit]"));
//         alert("AJAX rquest sending<br />") ;
            // fire off the request to /form.php
            request = $.ajax({
                url: "/album/exlprep4formsmod",
                type: "post",
                dataType: "json",
                processData: false,
                data: fd,
                contentType: false,
                success : function ( testReturn )
                  {
                	/*
                	 * testRetrun 
                	 * 
                	 * "mod_rows" => $mod_rows, 
                	 * "headingRow" => $headingRow, 
                	 * "sell_cols" => $sel_cols
                	 */
                        console.log("Hooray, it worked!");
                        var jsonString = jsonToString(testReturn);
//                    alert("response from AJAX response<br />") ;
//                    alert(testReturn) ;
//		            $('#result').html("response received!!!");			
		            $('#result').html(testReturn.headingRow);			
//		            $('#result').html(testReturn.sell_cols);			

                    var space = '<hr><div class = "row"> <div class="col-sm-2">';
                    space += '</div><div class="col-sm-12><span class="caret"></span></div></div>';
////                    var result_header ='<label class="col-sm-2 control-label">Result</label>';  
                    var result_header =$('<h4 class="col-sm-2 text-right">Not classified</h4>');  
//                    var result_header = '<h4> Testcases not classified </h4>';
			        var table =$('<table class="table tablesorter" id="nocat"> </table>');
			        /*
			         * TODO: parameterize the table head from JSON result
			         */
//			        var header  = $('<thead><tr><th>Casecode</th> <th>Title</th> <th>Problem</th> <th>Reproduction</th><th>Cause</th> <th>Measure</th></tr></thead>'); 
			        var header  = $('<thead></thead>'); 
		            var headrow = $('<tr class="nocategory"></tr>');
		            $.each(testReturn.headingRow, function() {
//		            	var newhead = $('<tr class="nocategory"></tr>');
		            	var th ='<th class="header">';
		            	th += this;
		            	th += '</th>';
		            	headrow.append(th);
		            	});
		            header.append(headrow);

			        var body = $('<tbody></tbody>')
			        table.append(header);
			        var tbody = table.append(body);
//		            $.each(testReturn, function() {
		            $.each(testReturn.mod_rows, function() {
		            	var newrow = $('<tr class="nocategory"></tr>');
		            	  $.each(this, function() {
		            		var td = '<td>';
		            		td += this;
		            		td += '</td>';
		            		newrow.append(td);
		            	  });
//		            	  table.append(newrow);
		            	  tbody.append(newrow);
		            	});
//		            $('#result').append(space);
//		            result_header.before($(space));
//		            $('#result').before(result_header);
		            $('#result').append(table);

                    $("#exldata input[name=submit]").removeClass("btn-primary");

                    $('.download').removeClass('hide');
		            $("#nocat").tablesorter();
                  },
                error : function (xhr, err)
                 {
                    alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
                    alert("responseText: "+xhr.responseText);
//                    $("#exldatasub input[name=submit]").removeClass("btn-info");
//                    $("#exldatasub input[name=submit]").css({ "color": "#333333",
//                    		  "background-color": "#b5b5b5",
//                    		  "border-color": "#b5b5b5"
//                    		});
//                	$("#response").html('A problem has occurred.');
                	// bootstrap popover tooltip.
//		            $("#exldatasub input[name='uploadTmp']").popover({title: 'Invalid template', placement: 'top', content: "make sure to use right text format"}); 
//                	$("#exldatasub input[name=uploadTmp]").addClass('popover1');
//                	$("#exldatasub input[name=uploadTmp]").popover('show');
//                    alert("failed ");
//                    console.log("Oops, it failed!");
//                        alert(data);
//                    alert("error from AJAX response<br />") ;
//                	$('body').on('click', function (e) {
//                		$("#exldatasub input[name=uploadTmp]").popover('hide');
//                	});
                 },
              });
             
//            return false;

            }); // end of $("#exldatasub").submit();
       
        /*
         * Spinner for ajax
         */
        // Creating spinner see <a href="http://fgnass.github.com/spin.js/">http://fgnass.github.com/spin.js/</a> for configuration wizzard
        var opts = {
           lines: 13, // The number of lines to draw
           length: 7, // The length of each line
           width: 4, // The line thickness
           radius: 10, // The radius of the inner circle
           rotate: 0, // The rotation offset
           color: '#0099FF', // #rgb or #rrggbb
           speed: 0.75, // Rounds per second
           trail: 50, // Afterglow percentage
           shadow: false, // Whether to render a shadow
           hwaccel: false, // Whether to use hardware acceleration
           className: 'spinner', // The CSS class to assign to the spinner
           zIndex: 2e9, // The z-index (defaults to 2000000000)
           top: 'auto', // Top position relative to parent in px
           left: 'auto' // Left position relative to parent in px
        };
        var spinner = new Spinner(opts);
        var ajax_cnt = 0; // Support for parallel AJAX requests
         
        // Global functions to show/hide on ajax requests
        $(document).ajaxStart(function() {
//           $('&lt;div id ="spinner_center" style="position:fixed;top:70px;left:49%;"&gt;&amp;nbsp;&lt;/div&gt;').appendTo('body');
           $('<div id ="spinner_center" style="position:fixed;top:70px;left:49%"></div>').appendTo('body');
//           $("#result").append($('<div id ="spinner_center" style="position:fixed;top:70px;left:49%"></div>'));
           spinner.spin($('#spinner_center')[0]);
//           ajax_cnt++;
        });
         
        $(document).ajaxStop(function() {
//           ajax_cnt--;
//           if (ajax_cnt <= 0) {
              spinner.stop();
              $('#spinner_center').remove();
//              ajax_cnt = 0;
//           }
        });

                 
        /*
        * First form to populate the 2nd form with a temalate file
        */
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
            fd.append("uploadExl",  $("#exldatasub input[name=uploadExl]")[0].files[0]);
//            fd.append("uploadtmp", $("#exldatasub input[name=submit]"));
//         alert("AJAX rquest sending<br />") ;
            // fire off the request to /form.php
            request = $.ajax({
                url: "/album/exlprep3formsmod",
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
//                    alert(jQuery.type(testReturn.applist)) ;
//                    alert(jQuery.type(testReturn.heading)) ;
//                        $("#demoeric").html(jsonString);
			            $('#result').html("response received!!!");			
			            $('#result').html(testReturn);			
                        
                        // Iteration to create table row
                        iter_insert_row(testReturn.applist);
                        iter_insert_checkbox(testReturn.heading, testReturn.searchCells);
//                        iter_insert_row_empty(testReturn);
                        $("#exldatasub input[name=submit]").removeClass("btn-info");

//                    alert("response from AJAX response<br />") ;
//                    alert(testReturn) ;
//                        $("#exldatasub").unbind('submit').submit();
                  },
                 complete: function() {
//                        $("#exldatasub input[type=submit]").unbind('submit');
                        $("#exldatasub input[type=submit]").attr('disabled', 'disabled');
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

            }); // end of $("#exldatasub").submit();

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
    
            // :helper function to create rows of checkboxes with a JSON data
        // 
        /** 
         * @memberOf iter_insert_checkbox
         */
        function iter_insert_checkbox(arr, checkedArr) {
            // :helper function to create rows of search item with a JSON data
        	
        	console.log("inserting checkboxes")
        	for (var i = 0; i < arr.length; i++) {
//	        	var cellCheck = '<input name="options[' + i + ']" class="cellCheck" type="checkbox" value="' + arr[i] + '">'; 
	        	var cellCheck = '<input name="options" class="cellCheck checkbox" type="checkbox" value="checkTest0"> checkonTheGo'; 
	        	if(jQuery.inArray(i, checkedArr) != -1 ) {
		        	$("#checkCells").append('<input type="checkbox" name="catChkBox[' + i + ']"  class="chkbox" \
		        		      id = "'+ i +'" value="'+ arr[i] + '" checked/>      <label for="'+ i +'">' + arr[i]+ '</label><br/>');

	        	} else {
		        	$("#checkCells").append('<input type="checkbox" name="catChkBox[' + i + ']" class="chkbox" \
		        		      id = "'+ i +'" value="'+ arr[i] + '" />      <label for="'+ i +'">' + arr[i]+ '</label><br/>');
	        	}
//	        	$("#checkCells").append('<li class="list-group-item"><input type="checkbox" name="catChkBox" class="ckbox" \
//	        		      id = "'+ i +'" value="'+ arr[i] + '" />      <label for="'+ i +'">' + arr[i]+ '</label><br/></li>');
//		        console.log(arr[i]);
//		        print_r(arr[i]);
//	        	$("#checkCells").append($(cellCheck));
        	}

        }
        
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