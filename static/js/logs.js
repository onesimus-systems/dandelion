/* global CategoryManage, $, document, window, setInterval, setTimeout, clearInterval, alert, tinymce */

"use strict"; // jshint ignore:line

var filt = false,
    refreshc;

$(document).ready(function() {
    $( "#datesearch" ).datepicker();
    $( "#datesearch" ).change(function() {
        $( "#datesearch" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
        });

    $("#add_edit").css("display", "none");
});

$(document).on("focusin", function(e) {
	if ($(e.target).closest(".mce-window").length) {
		e.stopImmediatePropagation();
	}
});

tinymce.init({
    browser_spellcheck: true
});

var refreshFun =
{
    /* This function is ran onload() of viewlog.php
     * It refreshes the log and then begins an interval
     * counter for every 2 minutes
     * This function can also be called on to restart
     * the autorefresh counter
     */
    startrefresh: function() {
        // Run first time
		refreshFun.refreshLog();
        CategoryManage.grabNextLevel('0:0');

		// Set timers
        refreshc = setInterval(function(){ refreshFun.refreshLog(); }, 60000);
    },

    stoprefresh: function() {
        clearInterval(refreshc);
    },
    /* This function updates the live feed.
     * If kindof == "update" it shows the recent log entries
     * logfilter.php and shows the returned output.
     */
    refreshLog: function(kindof) {
        if (!filt)
            {
                $.ajax({
                    type: "POST",
                    url: "api/i/logs/read",
                    async: false,
                    dataType: "json",
                    data: { action: "getLogs", data: "" }
                })
                    .done(function( html ) {
                        if (html === "") {
                            // PHP session timed out, use no longer logged in
                            window.location.reload(true);
                        }
                        view.makeLogView(html.data);
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
}; // refreshFun

var view = {
    makeLogView:function(data) {
        $('#refreshed').empty();
        $('#refreshed').append(view.pageControls(data.metadata));
        $('#refreshed').append(view.displayLogs(data));
        $('#refreshed').append(view.pageControls(data.metadata));
    },

    pageControls: function(data) {
        var div = $('<div/>').attr('class', 'pagination');

        var html = '<form method="post">';
        if (data.offset > 0) {
            var prevPage = data.offset-data.limit;
            html += '<input type="button" value="Previous '+data.limit+'" onClick="view.pagentation('+prevPage+');" class="flle">';
        }
        if (data.offset+data.limit < data.logSize) {
            var nextPage = data.offset+data.limit;
            html += '<input type="button" value="Next '+data.limit+'" onClick="view.pagentation('+ nextPage +');" class="flri">';
        }
        html += '</form></div>';
        div.append(html);
        return div;
    },

    displayLogs: function(data) {
        var div = $('<div/>').attr('id', 'refreshed_core');

        for (var key in data) {
            if (!data.hasOwnProperty(key) || key == 'metadata')
                continue;

            var log = data[key];

            var creator = log.realname;
            if (creator === '') {
                creator = 'Unknown User';
            }

            // Display each log entry
            // jshint multistr:true
            var html = '<form method="post">\
                        <div class="logentry">\
                        <h2>' + log.title + '</h2>\
                        <p class="entry">' + log.entry + '</p>\
                        <p class="entrymeta">Created by ' + creator + ' on ' + log.datec + ' @ ' + log.timec + ' ';

            if (log.edited == "1") { html += '(Edited)'; }

            html += '<br>Categorized as ' + log.cat + '\
                     <br><a href="#" onClick="searchFun.filter(\'' + log.cat + '\');">Learn more about this system...</a>';

            if (log.canEdit) { html += '<input type="button" value="Edit" onClick="editFun.grabedit(' + log.logid + ');" class="flri">'; }

            html += '</p></div></form>';

            div.append(html);
        }
        return div;
    },

    /* This function manages the pagentation of the
     * log. It receives the desired DB row offset
     * which is supplied by readlog.php then sends
     * the request to updatelog.php which handles
     * the SELECT limits.
     */
    pagentation: function(pageOffset) {
        $.post("api/i/logs/read", { offset: pageOffset }, null, "json")
            .done(function( html ) {
                view.makeLogView(html.data);

                if (pageOffset <= 0) {
                    refreshFun.startrefresh();
                } else {
                    refreshFun.stoprefresh();
                }

                window.scrollTo(0,0);
            })

            .fail(function( jqXHR ) {
                if ( typeof jqXHR !== 'undefined' && jqXHR.readyState===4 && jqXHR.status===404) {
                    $("#refreshed").html("An error has occured. Please try logging out and back in.");
                }
            });
    }
}; // View

var addFun =
{
    showaddinputs: function() {
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
            browser_spellcheck: true,
			forced_root_block: false,
			resize: false,
			menubar: "edit format view insert tools",
			toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
			plugins: [
				"autolink link lists hr anchor pagebreak",
				"searchreplace wordcount code insertdatetime",
				"template paste textcolor"
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
        var cat = CategoryManage.getCatString();

		if (title !== "" && entry !== "" && cat !== "") {
			$( "#add_edit" ).dialog( "close" );
			$("#messages").fadeOut();

			var logData = {
				cat: cat,
				add_title: title,
				add_entry: entry
			};

			$.post("lib/logs.php", { action: "addLog", data: JSON.stringify(logData) })
				.done(function( html ) {
					refreshFun.refreshLog();
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
            browser_spellcheck: true,
			forced_root_block: false,
			resize: false,
			menubar: "edit format view insert tools",
			toolbar: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor",
			plugins: [
				"autolink link lists hr anchor pagebreak",
				"searchreplace wordcount code insertdatetime",
				"template paste textcolor"
				]
        });
    },

    /* This function is called when a user clicks edit
     * It grabs the information about the log entry they
     * want to edit then calls showeditinputs(); to display
     * the fields.
     */
    grabedit: function(logid) {
        $.post("lib/logs.php", { action: 'getLogInfo', data: logid })
            .done( editFun.showeditinputs );
    },

    //Sends the finished edited log to a PHP file for processing
    editlogs: function(id) {
        var editedtitle = $("input#logTitle").val();
        editedtitle = encodeURIComponent(editedtitle);
        var editedlog = $("textarea#logEntry").val();
        editedlog = encodeURIComponent(editedlog);

        if (editedtitle !== "" && editedlog !== "") {
			$( "#add_edit" ).dialog( "close" );
			$("#messages").fadeOut();

			var logData = {
				editlog: editedlog,
				edittitle: editedtitle,
				choosen: id
			};

			$.post("lib/logs.php", { action: 'editLog', data: JSON.stringify(logData) })
				.done(function( html ) {
				refreshFun.refreshLog();
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
        var type = '';

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

        var search = {
			keyw: searchfor,
			dates: datefor,
			type: type
        };

        $.post("lib/logs.php", { action: 'filterLogs', data: JSON.stringify(search) })
            .done(function( html ) {
                filt=true;
                refreshFun.stoprefresh();
                $("#refreshed").html( html );
            });
    },

    // Search for a category of logs
    filter: function(cat) {
        if (cat === '') { cat = CategoryManage.getCatString(); }

        var filter = {
			type: '',
			filter: cat
        };

        if (cat)
        {
            $.post("lib/logs.php", { action: 'filterLogs', data: JSON.stringify(filter)})
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
