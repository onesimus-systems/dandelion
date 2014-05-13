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
    showaddinputs: function() {
        $("#add_edit_form")[0].reset();
        
        $( "#add_edit" ).dialog({
          height: 550,
          width: 800,
          modal: true,
          buttons: {
            "Add Log": function() {
                addFun.addlog();
                $( this ).dialog( "close" );
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
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
        
        $.post("scripts/add_log.php", { cat: cat, add_title: title, add_entry: entry })
            .done(function( html ) {
                refreshLog();
                secleft=120;
                CategoryManage.addLog = false;
                CategoryManage.grabNextLevel('0:0');
            });
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
          height: 550,
          width: 800,
          modal: true,
          buttons: {
            "Save Edit": function() {
                editFun.editlogs(linfo.logid);
                $( this ).dialog( "close" );
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
        
        editing = true;
    },

    /* This function is called when a user clicks edit
     * It grabs the information about the log entry they
     * want to edit then calls showeditinputs(); to display
     * the fields.
     */
    grabedit: function(logid) {
        $.post("scripts/logeditinfo.php", { loguid: logid })
            .done( editFun.showeditinputs );
    },

    //Sends the finished edited log to a PHP file for processing
    editlogs: function(id) {
        var editedtitle = $("input#logTitle").val();
        editedtitle = encodeURIComponent(editedtitle);
        var editedlog = $("textarea#logEntry").val();
        editedlog = encodeURIComponent(editedlog);
        
        $.post("scripts/editlogs.php", { editlog: editedlog, edittitle: editedtitle, choosen: id })
            .done(function( html ) {
                refreshLog();
                secleft=120;
            });
    },
}; //editFun

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