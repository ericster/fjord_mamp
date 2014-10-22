
	function Taed_highcharts(container, cat, dat, chart_title){
		if (cat.length < 10)  { barwidth = 40; }
		else { barwidth = null;}
	    $(container).highcharts({
	        chart: {
	            type: 'column'
	        },
	        colors: [
	                 '#4572A7', 
	                 '#89A54E', 
	                 '#A47D7C', 
	                 '#80699B', 
	                 '#3D96AE', 
	                 '#92A8CD', 
	                 '#B5CA92',
	                 '#DB843D', 
	                 '#AA4643' 
	                 ],
	        title: {
	            text: chart_title
	        },
	        xAxis: {
	            categories: cat
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Issues #' 
	            }
	        },
	        legend: {
	            reversed: true
	        },
	        plotOptions: {
	            series: {
	                stacking: 'normal',
                	pointWidth: barwidth //width of the column bars irrespective of the chart size
	            }
	        },
	        series: dat 
	    });
	}

	//function Taed_piechart(container, cat, dat, chart_title){
	function Taed_piechart(container){
	    $(container).highcharts({
	        chart: {
	            type: 'pie',
	            options3d: {
	                enabled: true,
	                alpha: 45,
	                beta: 0
	            }
	        },
	        title: {
	            text: 'Browser market shares at a specific website, 2014'
	        },
	        tooltip: {
	            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	        },
	        plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                depth: 35,
	                dataLabels: {
	                    enabled: true,
	                    format: '{point.name}'
	                }
	            }
	        },
	        series: [{
	            type: 'pie',
	            name: 'Browser share',
	            data: [
	                ['Firefox',   45.0],
	                ['IE',       26.8],
	                {
	                    name: 'Chrome',
	                    y: 12.8,
	                    sliced: true,
	                    selected: true
	                },
	                ['Safari',    8.5],
	                ['Opera',     6.2],
	                ['Others',   0.7]
	            ]
	        }]
		});
	}

	function sample_chart(){
	    $('#charts_container').highcharts({
	        chart: {
	            type: 'column'
	        },
	        title: {
	            text: 'Stacked bar chart'
	        },
	        xAxis: {
	            categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Total Issues'
	            }
	        },
	        legend: {
	            reversed: true
	        },
	        plotOptions: {
	            series: {
	                stacking: 'normal'
	            }
	        },
	        series: [{
	            name: 'John',
	            data: [5, 3, 4, 7, 2]
	        }, {
	            name: 'Jane',
	            data: [2, 2, 3, 2, 1]
	        }, {
	            name: 'Joe',
	            data: [3, 4, 4, 2, 5]
	        }]
	    });
	}

	function chart_draw(data){
            hparA = data.message.A;
            catA = hparA.cat;
            datA = hparA.dat;
            hparB = data.message.B;
            catB = hparB.cat;
            datB = hparB.dat;
            hparC = data.message.C;
            catC = hparC.cat;
            datC = hparC.dat;
            //$('#result').html(data.message);			
            Taed_highcharts('#chart_device_all',catA, datA, 'Total # of Issues per Device');
            Taed_highcharts('#chart_type_per_device',catB, datB, 'Issues by Type per Device');
            Taed_highcharts('#chart_type_per_app',catC, datC, 'Issue by Type per Application');
            Taed_piechart('#chart_issues_by_device');
	}

	function ConvertFormToJSON(form){
	    var array = jQuery(form).serializeArray();
	    var json = {};
	    
	    jQuery.each(array, function() {
	        json[this.name] = this.value || '';
	    });
	    
	    return json;
	}

    // :helper function to change JSON object to string 
    function jsonToString($jsonObject) {
            return JSON.stringify($jsonObject);
    }
    

    // :helper function adding the first row: not used 
    function insert_row_first( currentCount, app_name, search_term) {
        var template = '<input name="searchTerm[__index__][appName]" required="required" class="appname" type="text" value="__app_name__"> \
                <input name="searchTerm[__index__][regexPattern]" required="required" class="appregex" type="text" value="__search_term__">';
        template = template.replace(/__index__/g, currentCount);
            appn.val(app_name);
            appr.val(search_term);
    }

    // helper function to add an empty row
    function insert_row_empty() {
        var currentCount = $('form .devicename').length;
        var template = '<input name="deviceVal[__index__][deviceName]" required="required" class="devicename" type="text" value=""> \
                <input name="deviceVal[__index__][deviceList]" required="required" class="devicelist" type="text" value="">';
        var rowCount = $('.collectionTable tr').length;
        template = template.replace(/__index__/g, currentCount);
        // console.log(template);
        var str3 = "\<div\>";
        var res1 = str3.concat(template);
        var res = res1.concat('\</div\>');
        var del = '<button type="button" class="btn btn-danger btn-sm" id="del-button"> \
        <span class="glyphicon glyphicon-trash"></span></button> </td> </tr>';
        
        // Test HTML tag
        var row_empty = '<tr class="nameRegex"> <td> col1 </td> <td> col2 </td> <td>  \
                <button type="button" class="btn btn-danger btn-sm" id="del-button"> \
                <span class="glyphicon glyphicon-trash"></span> </button> </td> </tr>';
        var div_add = '<li> Hello World </li>'
        var div_add2 = '<li> Hello World2 </li>'
        var div_add3 = '<p> Hello World2 </p>'
       

        var tplate = $(template);
        var parsed = $('<div/>').append(tplate);
        var appn= parsed.find('input.devicename');
        var appr= parsed.find('input.devicelist');
        appn.addClass('input-normal')
        appr.addClass('input-xxlarge')
        var appn_td = $('<td></td>').append(appn);
        var appr_td = $('<td></td>').append(appr);
        var del_td = $('<td></td>').append(del); 
        var app_tr = $('<tr></tr>').append(appn_td).append(appr_td).append(del_td);


        // Appending to target tag
        $('.collectionTable tr:last').after(app_tr);
        bindAutoComplete();
        
        // Testing a table
        $('#myTable tr:last').after(row_empty);
        }
    
	function split( val ) {
	     return val.split( /,\s*/ );
	}
	function extractLast( term ) {
	    return split( term ).pop();
	 }

	function bindAutoComplete(){
		  $( ".devicelist" )
		    // don't navigate away from the field on tab when selecting an item
		    .bind( "keydown", function( event ) {
		      if ( event.keyCode === $.ui.keyCode.TAB &&
		          $( this ).autocomplete( "instance" ).menu.active ) {
		        event.preventDefault();
		      }
		    })
		    .autocomplete({
			  maxShowItems: 8, // Make list height fit to 5 items when items are over 5.
		      source: function( request, response ) {
		        $.getJSON( "/device/device-list", {
		          term: extractLast( request.term )
		        }, response );
		      },
		      search: function() {
		        // custom minLength
		        var term = extractLast( this.value );
		        if ( term.length < 2 ) {
		          return false;
		        }
		      },
		      focus: function (event, ui) {
		                $(".ui-helper-hidden-accessible").hide();
		                event.preventDefault();
		      },
		      select: function( event, ui ) {
		        var terms = split( this.value );
		        // remove the current input
		        terms.pop();
		        // add the selected item
		        terms.push( ui.item.value );
		        // add placeholder to get the comma-and-space at the end
		        terms.push( "" );
		        this.value = terms.join( ", " );
		        return false;
		      },
		      messages: {
		          noResults:"",
		          results: function() {}
		      }
		    });
	  }

$(document).ready(function() {
		
	$( "#devicemap" ).submit(function( event ) {
		  event.preventDefault();
		  var serializedData = $("#devicemap").serializeArray();
		  
		  request =   jQuery.ajax({
		        url : '/device/device-list-ajax',
		        type: 'POST',
		        dataType: 'JSON',
		        data: serializedData,
		        success: function(data, status){
		            //alert(data.message);
                    console.log(data.message);
                    chart_draw(data);
                    //sample_chart();
		            if(data.status == 'error'){
		                // Perform any operation on error
		            }else{
		                // Perform any operation on success
		            }
		        },
		        error : function(xhr, textStatus, errorThrown) {
		            if (xhr.status === 0) {
		                alert('Not connect.\n Verify Network.');
		            } else if (xhr.status == 404) {
		                alert('Requested page not found. [404]');
		            } else if (xhr.status == 500) {
		                alert('Server Error [500].');
		                console.log(xhr.status);
		                console.log(xhr.responseText);
		                console.log(thrownError);
		            } else if (errorThrown === 'parsererror') {
		                alert('Requested JSON parse failed.');
		            } else if (errorThrown === 'timeout') {
		                alert('Time out error.');
		            } else if (errorThrown === 'abort') {
		                alert('Ajax request aborted.');
		            } else {
	                    alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
	                    alert("responseText: "+xhr.responseText);
		            }
		        },
		        complete: function(){
		            // Perform any operation need on success/error
		        }
		    });
		});


   
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
       $('<div id ="spinner_center" style="position:fixed;top:70px;left:49%"></div>').appendTo('body');
       spinner.spin($('#spinner_center')[0]);
    });
     
    $(document).ajaxStop(function() {
          spinner.stop();
          $('#spinner_center').remove();
    });

             

    $(document).on("click", '#del-button', function(event){
            row = $(this).parent().parent();
            row.remove();
            event.preventDefault();
    });
    
            
    //    $(".table").tablesorter();
    
    $('.lead').hover(
                     function() {
                                $( this ).append( $( "<span> ***</span>" ) );
                              }, function() {
                                $( this ).find( "span:last" ).remove();
                              }
                    );
    // to add a new row
    $('.addbtn').on('click', function(e){
        e.preventDefault();
        insert_row_empty();
        bindAutoComplete();

    
        // Sample from ZF2 
        app2 =  $('form > div > fieldset').find('.appname');
        fset = $('form > div > fieldset');
    });

	$( ".devicelist" )
	  // don't navigate away from the field on tab when selecting an item
	.bind( "keydown", function( event ) {
	      if ( event.keyCode === $.ui.keyCode.TAB &&
	          $( this ).autocomplete( "instance" ).menu.active ) {
	        event.preventDefault();
	      }
	})
	.autocomplete({
		  maxShowItems: 8, // Make list height fit to 5 items when items are over 5.
	      source: function( request, response ) {
	        $.getJSON( "/device/device-list", {
	          term: extractLast( request.term )
	        }, response );
	      },
	      search: function() {
	        // custom minLength
	        var term = extractLast( this.value );
	        if ( term.length < 2 ) {
	          return false;
	        }
	      },
		  focus: function (event, ui) {
		                $(".ui-helper-hidden-accessible").hide();
		                event.preventDefault();
		  },
	      select: function( event, ui ) {
	        var terms = split( this.value );
	        // remove the current input
	        terms.pop();
	        // add the selected item
	        terms.push( ui.item.value );
	        // add placeholder to get the comma-and-space at the end
	        terms.push( "" );
	        this.value = terms.join( ", " );
	        return false;
	      },
	      messages: {
	          noResults:"",
	          results: function() {}
	      }
	});
  });