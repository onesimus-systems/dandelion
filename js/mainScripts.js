var autore = false,
    filt = false,
    title="",
    entry="",
    refreshinv,
    refreshc,
    secleft=120;

$(document).ready(function() {
    $( "#datesearch" ).datepicker();
    $( "#datesearch" ).change(function() {
        $( "#datesearch" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
        });
        
    $("#add_edit").css("display", "none");
});

$(document).on("focusin", function(e) {
	if ($(event.target).closest(".mce-window").length) {
		e.stopImmediatePropagation();
	}
});
    
var miscFun = {
    clearval: function(clearme) {
        clearme.value="";
    },
};
 
var refreshFun =
{
    /* This function is ran onload() of viewlog.phtml
     * It refreshes the log and then begins an interval
     * counter for every 2 minutes
     * This function can also be called on to restart
     * the autorefresh counter
     */
    startrefresh: function() {
        // Run first time
		refreshLog("update");
		if (typeof presence !== 'undefined') {
            setTimeout(function(){presence.checkstat(0);}, 1);
		}
        CategoryManage.grabNextLevel('0:0');
		
		// Set timers
        refreshc = setInterval(function(){refreshLog("update");}, 120000);
		if (typeof presence !== 'undefined') {
            wherearewe = setInterval(function(){presence.checkstat(0);}, 30000);
		}

        autore = true;
    },

    stoprefresh: function() {
        clearInterval(refreshc);
        autore = false;
    },
}; // refreshFun

/* This function updates the live feed.
 * If kindof == "update" it shows the recent log entries
 * logfilter.php and shows the returned output.
 */
function refreshLog(kindof) {
    if (!filt)
        {
            $.ajax({
                url: "scripts/updatelog.php",
                async: false
            })
                .done(function( html ) {
                    $("#refreshed").html( html );
                })

                .fail(function( jqXHR ) {
                    if ( typeof jqXHR !== 'undefined' && jqXHR.readyState===4 && jqXHR.status===404)
                    {
                        $("#refreshed").html("An error has occured. Please try logging out and back in.");
                    }
                });
        }
    
    if (kindof==="clearf")
        {
            filt=false;
            $("#searchterm").val("Keyword");
            $("#datesearch").val("Date");
            refreshFun.startrefresh();
        }
}

/* This function manages the pagentation of the
 * log. It receives the desired DB row offset
 * which is supplied by readlog.php then sends
 * the request to updatelog.php which handles
 * the SELECT limits.
 */
function pagentation(pageOffset) {
    $.post("scripts/updatelog.php", { pageOffset: pageOffset})
        .done(function( html ) {
            $("#refreshed").html( html );
            
            if (pageOffset <= 0)
            {
                refreshLog('clearf');
            }
            else
            {
                refreshFun.stoprefresh();
            }
            
            window.scrollTo(0,0);
        })
        
        .fail(function( jqXHR ) {
            if ( typeof jqXHR !== 'undefined' && jqXHR.readyState===4 && jqXHR.status===404)
            {
                $("#refreshed").html("An error has occured. Please try logging out and back in.");
            }
        });
}

var addFun =
{
    showaddinputs: function(title, entry) {
        $("#add_edit_form")[0].reset();
        $("textarea#logEntry").html("");
        
        $( "#add_edit" ).dialog({
			height: 575,
			width: 800,
			modal: true,
			show: {
				effect: "fade",
				duration: 500
			},
			hide: {
				effect: "fade",
				duration: 500
			},
			buttons: {
				"Add Log": function() {
					addFun.addlog();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					CategoryManage.addLog = false;
					CategoryManage.grabNextLevel('0:0');
				}
			}
        });
        
        $("textarea#logEntry").tinymce({
			forced_root_block: false,
			resize: false,
			menubar: "edit format view insert tools",
			toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
			plugins: [
				"autolink link lists hr anchor pagebreak spellchecker",
				"searchreplace wordcount code insertdatetime",
				"contextmenu template paste textcolor"
				]
        });

        CategoryManage.addLog = true;
        CategoryManage.grabNextLevel('0:0');
    },

    /* This function sends details for a new log entry to
     * add_log.php. It then refreshes the Live feed.
     */
    addlog: function() {    
        var title = $("input#logTitle").val();
        title = encodeURIComponent(title);
        var entry = $("textarea#logEntry").val();
        entry = encodeURIComponent(entry);
        cat = CategoryManage.getCatString();
        
		if (title != "" && entry != "" && cat != "") {
			$( "#add_edit" ).dialog( "close" );
			$("#messages").fadeOut();
			
			logData = {
				cat: cat,
				add_title: title,
				add_entry: entry
			};

			$.post("scripts/logs.php", { action: "addLog", data: JSON.stringify(logData) })
				.done(function( html ) {
					refreshLog();
					secleft=120;
					CategoryManage.addLog = false;
					CategoryManage.grabNextLevel('0:0');
					showDialog(html);
				});
        }
        else {
			$("input#logTitle").val( decodeURIComponent(title) );
			$("textarea#logEntry").html( decodeURIComponent(entry) );
			$("#messages").html('<span class="bad">Log entries must have a title, category, and entry text.</span>').fadeIn();
			CategoryManage.addLog = true;
			CategoryManage.grabNextLevel('0:0');
			setTimeout(function() { $("#messages").fadeOut(); }, 10000);
        }
    },
}; //addFun

var editFun =
{
    showeditinputs: function(log_info) {
        
        var linfo = JSON.parse(log_info);
        
        $("input#logTitle").val( linfo.title );
        $("textarea#logEntry").val( linfo.entry );
        $("#catSpace").text( linfo.cat );
        
        $( "#add_edit" ).dialog({
			height: 575,
			width: 800,
			modal: true,
			show: {
				effect: "fade",
				duration: 500
			},
			hide: {
				effect: "fade",
				duration: 500
			},
			buttons: {
				"Save Edit": function() {
					editFun.editlogs(linfo.logid);
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
        });
        
        $("textarea#logEntry").tinymce({
			forced_root_block: false,
			resize: false,
			menubar: "edit format view insert tools",
			toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
			plugins: [
				"autolink link lists hr anchor pagebreak spellchecker",
				"searchreplace wordcount code insertdatetime",
				"contextmenu template paste textcolor"
				]
        });
        
        editing = true;
    },

    /* This function is called when a user clicks edit
     * It grabs the information about the log entry they
     * want to edit then calls showeditinputs(); to display
     * the fields.
     */
    grabedit: function(logid) {
        $.post("scripts/logs.php", { action: 'getLogInfo', data: logid })
            .done( editFun.showeditinputs );
    },

    //Sends the finished edited log to a PHP file for processing
    editlogs: function(id) {
        var editedtitle = $("input#logTitle").val();
        editedtitle = encodeURIComponent(editedtitle);
        var editedlog = $("textarea#logEntry").val();
        editedlog = encodeURIComponent(editedlog);
        
        if (editedtitle != "" && editedlog != "") {
			$( "#add_edit" ).dialog( "close" );
			$("#messages").fadeOut();
			
			logData = {
				editlog: editedlog,
				edittitle: editedtitle,
				choosen: id
			};
			
			$.post("scripts/logs.php", { action: 'editLog', data: JSON.stringify(logData) })
				.done(function( html ) {
				refreshLog();
				secleft=120;
				showDialog(html);
			});
		}
		
        else {
			$("#messages").html('<span class="bad">Log entries must have a title, category, and entry text.</span>').fadeIn();
			setTimeout(function() { $("#messages").fadeOut(); }, 10000);
        }
    },
}; //editFun

function showDialog( html ) {
	$( "#dialog" ).html( "<p>"+html+"</p>" );
	$( "#dialog" ).dialog({
		modal: true,
		width: 400,
		show: {
			effect: "fade",
			duration: 500
		},
		hide: {
			effect: "fade",
			duration: 500
		},
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
}

var searchFun =
{
    // Checks if enter key was pressed, if so search
    check: function(e) {
        if (e.keyCode == 13) {
            searchFun.searchlog();
        }
    },

    // Search for keyword or datestamp
    searchlog :function() {
        var searchfor = $("input#searchterm").val();
        var datefor   = $("input#datesearch").val();
        searchfor     = encodeURIComponent(searchfor);

        if (searchfor!=="" && searchfor!=="Keyword" && searchfor!==null && datefor!=="" && datefor!=="Date" && datefor!==null) {
            type = "both";
        }
        else if (searchfor!=="" && searchfor!=="Keyword" && searchfor!==null) {
            type = "keyw";
        }
        else if (datefor!=="" && datefor!=="Date" && datefor!==null) {
            type = "dates";
        }
        else {
            alert("Please enter valid search criteria.");
            return false;
        }
        
        $.post("scripts/logfilter.php", { keyw: searchfor, dates: datefor, type: type })
            .done(function( html ) {
                filt=true;
                refreshFun.stoprefresh();
                $("#refreshed").html( html );
            });
    },
    
    // Search for a category of logs
    filter: function(cat) {
        if (cat === '') { cat = CategoryManage.getCatString(); }
        
        if (cat)
        {
            $.post("scripts/logfilter.php", { filter: cat})
                .done(function( html ) {
                    $("#refreshed").html( html );
                    filt=true;
                    refreshFun.stoprefresh();
                })
                
                .fail(function( jqXHR ) {
                    if ( typeof jqXHR !== 'undefined' && jqXHR.readyState===4 && jqXHR.status===404)
                    {
                        $("#refreshed").html("An error has occured. Please try logging out and back in.");
                    }
                });
        }
        else {
            alert("Please select a valid filter.");
        }
    },
};