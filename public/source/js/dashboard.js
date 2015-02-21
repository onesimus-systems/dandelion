/* global CategoryManage, $, document, window, setInterval, setTimeout, clearInterval, tinymce */

"use strict"; // jshint ignore:line

var search = false,
    refreshc;

$(document).ready(function() {
    var dateSearch = $('#datesearch');
    dateSearch.datepicker();
    dateSearch.change(function() {
        $('#datesearch').datepicker('option', 'dateFormat', 'yy-mm-dd');
        });

    searchFun.enableclear();
    searchFun.focusOut();
});

$(document).on('focusin', function(e) {
	if ($(e.target).closest('.mce-window').length) {
		e.stopImmediatePropagation();
	}
});

tinymce.init({
    browser_spellcheck: true
});

var refreshFun =
{
    runFirst: function() {
        refreshFun.refreshLog();
        refreshFun.startrefresh();
    },

    startrefresh: function() {
        refreshc = setInterval(function(){ refreshFun.refreshLog(); }, 60000);
    },

    stoprefresh: function() {
        clearInterval(refreshc);
    },

    refreshLog: function(clearSearch) {
        if (clearSearch) {
            searchFun.enableclear();
            refreshFun.startrefresh();
        }

        if (!search) {
            $.get('api/i/logs/read', {}, null, 'json')
                .done(function(json) {
                    view.makeLogView(json.data);
                })
                .fail(function(response) {
                    if (typeof response !== 'undefined' && response.readyState===4 && response.status===404)
                    {
                        $('#logs').html('An error has occured. Please try logging out and back in.');
                    }
                });
        }
    }
}; // refreshFun

var view = {
    makeLogView:function(data) {
        var logView = $('#logs');
        logView.empty();
        logView.append(view.pageControls(data.metadata, 'top'));
        logView.append(view.displayLogs(data));
        logView.append(view.pageControls(data.metadata, 'bottom'));
    },

    pageControls: function(data, pos) {
        var div = $('<div/>').attr('class', 'pagination');
        var clickAction;

        var html = '<form method="post">';
        if (data.offset > 0) {
            var prevPage = data.offset-data.limit;
            if (search) {
                clickAction = 'searchFun.searchlog('+ prevPage +');';
            } else {
                clickAction = 'view.pagentation('+ prevPage +');';
            }
            html += '<input type="button" value="Previous" onClick="'+ clickAction +'" class="flle">';
        }

        if (data.offset+data.limit < data.logSize && data.resultCount == data.limit) {
            var nextPage = data.offset+data.limit;
            if (search) {
                clickAction = 'searchFun.searchlog('+ nextPage +');';
            } else {
                clickAction = 'view.pagentation('+ nextPage +');';
            }
            html += '<input type="button" value="Next" onClick="'+ clickAction +'" class="flri">';
        }

        if (search && pos == 'top') {
            html += '<input type="button" value="Clear Search" onClick="refreshFun.refreshLog(true);" class="flri">';
        }
        html += '</form></div>';
        div.append(html);
        return div;
    },

    displayLogs: function(data) {
        var div = $('<div/>').attr('id', 'logs_core');

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

            html += '<br>Categorized as ' + log.cat + '<br><a href="#" onClick="searchFun.filter(\'' + log.cat + '\');">Learn more about this system...</a>';

            if (log.canEdit) { html += '<input type="button" value="Edit" onClick="editFun.grabedit(' + log.logid + ');" class="flri">'; }

            html += '</p></div></form>';

            div.append(html);
        }
        return div;
    },

    pagentation: function(pageOffset) {
        $.get('api/i/logs/read', { offset: pageOffset }, null, 'json')
            .done(function(html) {
                view.makeLogView(html.data);

                if (pageOffset <= 0) {
                    refreshFun.refreshLog();
                    refreshFun.startrefresh();
                } else {
                    refreshFun.stoprefresh();
                }

                window.scrollTo(0,0);
            })
            .fail(function(jqXHR) {
                if (typeof jqXHR !== 'undefined' && jqXHR.readyState===4 && jqXHR.status===404) {
                    $('#logs').html('An error has occured. Please try logging out and back in.');
                }
            });
    }
}; // View

var addFun =
{
    showaddinputs: function() {
        var entryBox = $('textarea#logEntry');
        $('#add_edit_form')[0].reset();
        entryBox.html('');

        $('#add_edit').dialog({
			height: 575,
			width: 800,
			modal: true,
			show: {
				effect: 'fade',
				duration: 500
			},
			hide: {
				effect: 'fade',
				duration: 500
			},
			buttons: {
				'Add Log': function() {
					addFun.sendLog(true);
				},
				Cancel: function() {
					$(this).dialog('close');
					CategoryManage.addEditLog = false;
					CategoryManage.grabFirstLevel();
				}
			}
        });

        entryBox.tinymce({
            browser_spellcheck: true,
			forced_root_block: false,
			resize: false,
			menubar: 'edit format view insert tools',
			toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor',
			plugins: [
				'autolink link lists hr anchor pagebreak',
				'searchreplace wordcount code insertdatetime',
				'template paste textcolor'
				]
        });

        CategoryManage.addEditLog = true;
        CategoryManage.grabFirstLevel();
    },

    sendLog: function(isnew, id) {
        var urlvars = {};
        var url = '';
        var title = $('input#logTitle').val();
        title = encodeURIComponent(title);
        var entry = $('textarea#logEntry').val();
        entry = encodeURIComponent(entry);
        var cat = CategoryManage.getCatString();

        if (isnew) {
            urlvars = { title: title, body: entry, cat: cat };
            url = 'api/i/logs/create';
        } else {
            urlvars = { logid: id, title: title, body: entry, cat: cat };
            url = 'api/i/logs/edit';
        }

		if (title !== '' && entry !== '' && cat !== '' && cat !== false) {
			$('#add_edit').dialog('close');
			$('#messages').fadeOut();

			$.post(url, urlvars, null, 'json')
				.done(function(html) {
					refreshFun.refreshLog();
					CategoryManage.addEditLog = false;
					CategoryManage.grabFirstLevel();
					showDialog(html.data);
				});
        } else {
			$('#messages').html('<span class="bad">Log entries must have a title, category, and entry text.</span>').fadeIn();
			CategoryManage.addEditLog = true;
			CategoryManage.grabFirstLevel();
			setTimeout(function() { $('#messages').fadeOut(); }, 10000);
        }
    }
}; //addFun

var editFun =
{
    showeditinputs: function(log_info) {
        var linfo = log_info.data;
        var entryBox = $('textarea#logEntry');

        $('input#logTitle').val(linfo.title);
        entryBox.val(linfo.entry);
        $('#catSpace').text('Loading categories...');

        CategoryManage.renderCategoriesFromString(linfo.cat, function(html) {
            CategoryManage.addEditLog = true;
            $('#categorySelects').empty();
            $('#catSpace').html(html);
        });

        $('#add_edit').dialog({
			height: 575,
			width: 800,
			modal: true,
			show: {
				effect: 'fade',
				duration: 500
			},
			hide: {
				effect: 'fade',
				duration: 500
			},
			buttons: {
				'Save Edit': function() {
                    addFun.sendLog(false, linfo.logid);
				},
				Cancel: function() {
					$(this).dialog('close');
                    CategoryManage.addEditLog = false;
                    CategoryManage.grabFirstLevel();
				}
			}
        });

        entryBox.tinymce({
            browser_spellcheck: true,
			forced_root_block: false,
			resize: false,
			menubar: 'edit format view insert tools',
			toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link | forecolor',
			plugins: [
				'autolink link lists hr anchor pagebreak',
				'searchreplace wordcount code insertdatetime',
				'template paste textcolor'
				]
        });
    },

    /* This function is called when a user clicks edit
     * It grabs the information about the log entry they
     * want to edit then calls showeditinputs(); to display
     * the fields.
     */
    grabedit: function(logid) {
        $.post('api/i/logs/readone', { logid: logid }, null, 'json')
            .done(editFun.showeditinputs);
    }
}; //editFun

function showDialog(html) {
    var dialog = $('#dialogBox');
	dialog.html('<p>'+html+'</p>');
	dialog.dialog({
		modal: true,
		width: 400,
		show: {
			effect: 'fade',
			duration: 500
		},
		hide: {
			effect: 'fade',
			duration: 500
		},
		buttons: {
			Ok: function() {
				$(this).dialog('close');
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
            e.preventDefault();
        }
    },

    enableclear: function() {
        search = false;
        $('#searchquery').val('Search');
        $('#searchquery').click(function() {
            var search = $('#searchquery');
            search.val('');
            search.off('click');
        });
    },

    focusOut: function() {
        $('#searchquery').focusout(function () {
            var search = $('#searchquery');
            if (search.val() === '') {
                searchFun.enableclear();
            }
        });
    },

    // Search for keyword or datestamp
    searchlog: function(offset) {
        if (typeof offset === 'undefined') {
            offset = 0;
        }
        var query = $('input#searchquery').val();

        $.post('api/i/logs/search', {query: query, offset: offset}, null, 'json')
            .done(function(json) {
                search = true;
                refreshFun.stoprefresh();
                view.makeLogView(json.data, true);
            });
    }
};
