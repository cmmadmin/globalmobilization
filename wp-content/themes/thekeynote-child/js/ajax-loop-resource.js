jQuery(document).ready(function ($) {
    var page = 1;
    var loading = true;
    var refresh = true;
    var noresults = false;
    var $window = $(window);
    var $content = $("#resource-content");
    var filter_region = [];
    var filter_moblevel = [];
	var filter_language = [];
    var sort_by = 'Newest';
    
    var load_posts = function(){
            $.ajax({
                type       : "GET",
                data       : { numPosts : 4, pageNumber: page,
					           regionArray : filter_region, mobLevelArray : filter_moblevel, languageArray : filter_language, sortBy : sort_by },
                dataType   : "html",
                url        : ajax_loop_vars.template_path + "/loop-resource.php",
                beforeSend : function() {
                	if( !noresults ) {
	                	var loader = "<div id='loading_gif'><img src='" + ajax_loop_vars.template_path + "/images/ajax-loader.gif' /></div>";
	                	if( refresh )
	                		$content.prepend( loader );
	                	else
	                		$content.append( loader );
                	}
                },
                success    : function(data){ // return data object from server
                    $data = $(data);
                    if( $data.length ) {
                        $data.hide();
                        if( refresh ) {
                        	$content.html($data);
                        	refresh = false;
                        	noresults = false;
                        } else {
                        	$content.append($data); // append to content container
                        }
                        $data.fadeIn(500, function() {
                            $("#loading_gif").remove(); // clean up loading gif
                            loading = false;
                        });
                    } else { // no results
                    	if( refresh ) {
                        	$content.html("<p>No results found.</p>");
                        	refresh = false;
                        }
                        $("#loading_gif").remove(); // clean up loading gif
                        loading = false;
                        noresults = true;
                    }
                },
                error     : function(jqXHR, textStatus, errorThrown) {
                    $("#loading_gif").remove();
                    alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                }
        });
    }
    // Sort
    $(".sort-item").click(function() {
    	sort_by = $(this).attr('data-sortby');
    	
    	$( "#current-sort" ).html( sort_by );
    	$(this).parent().find( ".selected-menu-item" ).replaceWith( "<a>" + $(this).parent().find( ".selected-menu-item" ).html() + "</a>" );
    	$(this).html( "<span class='selected-menu-item'>" + sort_by + "</span>" );

		loading = true;
		refresh = true;
		noresults = false;
		page = 1;
		load_posts();
    });    
    
    // Add term to filter
    $(".menu-item").click(function() {
    	var mytax = $(this).attr('data-tax');
    	var myterm = $(this).attr('data-term');
    	
       	getFilterArray(mytax).push(myterm);
       	
    	$( "#filter-legend" ).append( "<span class='legend-item' data-tax='" + mytax + "' data-term='" + myterm + "'><a class='halflings remove'></a>" + $(this).html() + "</span>" );

		loading = true;
		refresh = true;
		noresults = false;
		page = 1;
		load_posts();
    });

    // Taxonomy link in resource stats section. Clear filter, then add term to filter
    $("#content-wrap").on("click", "a.tax-link", function() {
    	var mytax = $(this).attr('data-tax');
    	var myterm = $(this).attr('data-term');
    	
    	clearAllFilterArrays();
    	getFilterArray(mytax).push(myterm);
    	
    	$( "#filter-legend" ).html( "<span class='legend-item' data-tax='" + mytax + "' data-term='" + myterm + "'><a class='halflings remove'></a>" + $(this).html() + "</span>" );

		loading = true;
		refresh = true;
		noresults = false;
		page = 1;
		load_posts();
    });
    
    // Remove term from filter
	$("#filter-legend").on("click", "span", function() {
    	var mytax = $(this).attr('data-tax');
    	var myterm = $(this).attr('data-term');
    	
    	var myarray = getFilterArray(mytax);
    	var index = myarray.indexOf(myterm);
    	myarray.splice(index, 1);
    	
    	$(this).remove();

		loading = true;
		refresh = true;
		noresults = false;
		page = 1;
		load_posts();
    });
	
	function getFilterArray( tax )
	{
    	switch( tax ) {
	        case "region":
	        	return filter_region;
	        case "mobilizationlevel":
	        	return filter_moblevel;
			case "language":
				return filter_language;
    	}
	}
	function clearAllFilterArrays()
	{
		filter_region.length = 0;
		filter_moblevel.length = 0;
		filter_language.length = 0;
	}
	
	// Add content on scroll
    $window.scroll(function() {
        var content_offset = $content.offset();
        //console.log("content_offset.top: " + content_offset.top + " $window.scrollTop: " + $window.scrollTop() + " $content.scrollTop: " + $content.scrollTop() + " $window.height: " + $window.height() + " $content.height: " + $content.height());
        if( !loading && 
        	($window.scrollTop() + $window.height()) > ($content.scrollTop() + $content.height() + content_offset.top - 500)) { // check location of scroll
                loading = true;
                page++;
                load_posts();
        }
    });
    load_posts();
});

