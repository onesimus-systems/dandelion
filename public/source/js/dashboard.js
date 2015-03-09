/**
 * Scripts for Dashboard
 */
/* global document, window, searchFun, $, setInterval,
    setTimeout, clearInterval, Categories, Cheesto */

"use strict"; // jshint ignore:line

var search = false,
    refreshc;

$(document).ready(function() {
    Refresh.init();
    View.init();
    Search.init();
    Cheesto.dashboardInit();

    $('#show-cheesto-button').click(function() {
        Section.show(this, 'messages-panel');
    });

    $('#show-logs-button').click(function() {
        Section.show(this, 'logs-panel');
    });
});

var Refresh =
{
    init: function() {
        Refresh.refreshLog();
        Refresh.startrefresh();
    },

    startrefresh: function() {
        refreshc = setInterval(function(){ Refresh.refreshLog(); }, 60000);
    },

    stoprefresh: function() {
        clearInterval(refreshc);
    },

    refreshLog: function(clearSearch) {
        if (clearSearch) {
            search = false;
            Refresh.startrefresh();
        }

        if (!search) {
            $.getJSON('api/i/logs/read', {}, function(json) {
                View.makeLogView(json.data);
            });
        }
    }
}; // Refresh

var Section =
{
    show: function(elem, panel) {
        if (elem.innerHTML.match(/^Show\s/)) {
            elem.innerHTML = elem.innerHTML.replace(/^Show\s/, 'Hide ');
        } else {
            elem.innerHTML = elem.innerHTML.replace(/^Hide\s/, 'Show ');
        }

        $('#'+panel).toggleClass('enabled');
    }
};

var View =
{
    prevPage: -1,
    nextPage: -1,
    currentOffset: -1,

    init: function() {
        $('#prev-page-button').click(View.loadPrevPage);
        $('#next-page-button').click(View.loadNextPage);
        $('#create-log-button').click(AddEdit.showAddInputs);
        $('#clear-search-button').click(function() { Refresh.refreshLog(true); });
    },

    makeLogView: function(data) {
        var logView = $('#log-list');
        logView.replaceWith(View.displayLogs(data));
        View.pageControls(data.metadata);
        View.currentOffset = data.metadata.offset;
    },

    displayLogs: function(data) {
        var logs = '<div id="log-list">';

        for (var key in data) {
            if (!data.hasOwnProperty(key) || key == 'metadata')
                continue;

            var log = data[key];

            var creator = log.realname;
            if (creator === '') {
                creator = 'Unknown User';
            }

            // Display each log entry
            var html = '<div class="log-entry">'+
                '<span class="log-title">'+ log.title +'</span>';

            if (log.canEdit) { html += '<button type="button" class="button edit-button" onClick="AddEdit.getEdit(' + log.logid + ');">Edit</button>'; }

            html += '<p class="log-body">'+ log.entry +'</p><p class="log-metadata">'+
                    '<span class="log-meta-author">Created by ' + creator + ' on ' + log.datec + ' @ ' + log.timec + ' ';

            if (log.edited == "1") { html += '(Amended)'; }

            html += '</span><span class="log-meta-cat">Categorized as <a href="#" onClick="searchFun.filter(\'' + log.cat + '\');">'+ log.cat +'</a></span></p></div>';

            logs += html;
        }
        logs += '</div>';
        return logs;
    },

    pageControls: function(data) {
        if (data.offset > 0) {
            View.prevPage = data.offset-data.limit;
        } else {
            View.prevPage = -1;
        }

        if (data.offset+data.limit < data.logSize && data.resultCount == data.limit) {
            View.nextPage = data.offset+data.limit;
        } else {
            View.nextPage = -1;
        }

        if (search) {
            $('#clear-search-button').show();
        } else {
            $('#clear-search-button').hide();
        }
    },

    loadPrevPage: function() {
        if (View.prevPage >= 0) {
            if (search) {
                Search.searchLog(View.prevPage);
            } else {
                View.pagentation(View.prevPage);
            }
        }
    },

    loadNextPage: function() {
        if (View.nextPage >= 0) {
            if (search) {
                Search.searchLog(View.nextPage);
            } else {
                View.pagentation(View.nextPage);
            }
        }
    },

    pagentation: function(pageOffset) {
        $.getJSON('api/i/logs/read', {offset: pageOffset}, function(json) {
            View.makeLogView(json.data);

            if (pageOffset <= 0) {
                Refresh.refreshLog();
                Refresh.startrefresh();
            } else {
                Refresh.stoprefresh();
            }
        });
    }
}; // View

var Search =
{
    init: function() {
        $('#search-btn').click(Search.searchLog);
        $('#search-query').on('keypress', Search.check);
    },

    // Checks if enter key was pressed, if so search
    check: function(e) {
        if (e.keyCode == 13) {
            Search.searchLog();
            e.preventDefault();
        }
    },

    // Search for keyword or datestamp
    searchLog: function(offset) {
        if (typeof offset === 'undefined') {
            offset = 0;
        }
        var query = $('#search-query').val();

        $.post('api/i/logs/search', {query: query, offset: offset}, function(json) {
            search = true;
            Refresh.stoprefresh();
            View.makeLogView(json.data);
        }, 'json');
    }
}; // Search

var AddEdit =
{
    showDialog: function(title, okText, okCall) {
        var dialogButtons = {};
        dialogButtons[okText] = okCall;
        dialogButtons.Cancel = function() { $(this).dialog('close'); };

        $('#add-edit-form').dialog({
            height: 450,
            width: 800,
            title: 'Edit Log',
            modal: true,
            open: function(evt, ui) {
                $('#log-body').htmlarea({
                    toolbar: [
                        ["bold", "italic", "underline", "strikethrough", "|", "forecolor"],
                        ["p", "h1", "h2", "h3", "h4", "h5", "h6"],
                        ["link", "unlink", "|", "orderedList", "unorderedList", "|", "superscript", "subscript"]
                    ],
                    css: 'assets/js/vendor/jhtmlarea/styles/jHtmlArea.Editor.css'
                });
                $('#log-body').htmlarea('updateHtmlArea');
            },
            show: {
                effect: 'fade',
                duration: 500
            },
            hide: {
                effect: 'fade',
                duration: 500
            },
            buttons: dialogButtons
        });
    },

    showEditInputs: function(logInfo) {
        $('#log-title').val(logInfo.data.title);
        $('#log-body').html(logInfo.data.entry);
        $('#categories').text('Loading categories...');

        Categories.renderCategoriesFromString(logInfo.data.cat, function(html) {
            $('#categories').html(html);
        });

        AddEdit.showDialog('Edit Log', 'Save Edit', function() {
            AddEdit.saveLog(false, logInfo.data.logid);
        });
    },

    showAddInputs: function() {
        $('#log-title').val('');
        $('#log-body').html('');
        $('#categories').html('');

        Categories.grabFirstLevel('categories');

        AddEdit.showDialog('Create Log', 'Save Log', function() {
            AddEdit.saveLog(true);
        });
    },

    getEdit: function(logid) {
        $.post('api/i/logs/readone', {logid: logid}, AddEdit.showEditInputs, 'json');
    },

    saveLog: function(isnew, id) {
        var urlvars = {};
        var url = '';
        var title = $('#log-title').val();
        var entry = $('#log-body').val();
        var cat = Categories.getCatString();

        if (isnew) {
            urlvars = { title: title, body: entry, cat: cat };
            url = 'api/i/logs/create';
        } else {
            urlvars = { logid: id, title: title, body: entry, cat: cat };
            url = 'api/i/logs/edit';
        }

        if (title !== '' && entry !== '' && cat !== '' && cat !== false) {
            $.post(url, urlvars, function(json) {
                    Refresh.refreshLog();
                    $.alert(json.data, 'Create Log', function() {
                        $('#add-edit-form').dialog('close');
                    });
                }, 'json');
        } else {
            $('#messages').html('<span class="bad">Log entries must have a title, category, and entry text.</span>').fadeIn();
            setTimeout(function() { $('#messages').fadeOut(); }, 10000);
        }
    }
}; // AddEdit
