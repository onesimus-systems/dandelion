var clearinput = true,
    editing = false,
    autore = true,
    filt = false,
    cat1="",
    cat2="",
    cat3="",
    cat4="",
    cat5="",
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
}); 
    
var miscFun = {
    //clears the add_edit div element
    clearaddedit: function() {
        document.getElementById("add_edit").innerHTML="";
    },

    //clears filter select elements
    clearfilt: function() {
        document.getElementById("f_cat_1").value = "select";
        cat1 = document.getElementById("f_cat_1");
        f_pop_cat_2(cat1);
        f_pop_cat_3(cat1);
        f_pop_cat_4(cat1);
        f_pop_cat_5(cat1);
    },
    
    clearval: function(clearme) {
        clearme.value="";
    },
}
 
var refreshFun = {
    //This function is ran onload() of viewlog.phtml
    //It refreshes the log and then begins an interval
    //counter for every 2 minutes
    //This function can also be called on to restart
    //the autorefresh counter
    startrefresh: function() {
        refreshc = setInterval(refreshLog("update"), 120000);
        wherearewe = setInterval(presence.checkstat(0), 30000);
        autore = true;
        //refreshLog("update");
        //presence.checkstat(0);
    },
} //refreshFun

//This function updates the live feed.
//If kindof == "update" it shows the recent log entries
//If kindof == "filter" it sends the filter details to
//logfilter.php and shows the returned output.
function refreshLog(kindof) {
    success = function()
	    {
		    document.getElementById("refreshed").innerHTML=$responseText;
		    
		    if (clearinput && !editing) {
		        document.getElementById("add_edit").innerHTML="";
		    }
		    else {
		        clearinput = true;
		    }
		    
		    if (filt) {
		        miscFun.clearfilt();
		    }
	    }
    failure = function()
	    {
	    	if (requestID.readyState===4 && requestID.status===404)
	        {
		        document.getElementById("refreshed").innerHTML="";
		        document.location.href = 'index.php';
	        }
	    }
    
    if (kindof==="update" && !filt)
        {
            address = 'scripts/updatelog.php';
            ajax2(address, '', success, failure);
        }
    /*else if (kindof==="filter")
        {
            cat1 = document.getElementById("f_cat_1").value;
            
            if (cat1 != null && cat1 != "" && cat1 != "select") {
                cat2 = document.getElementById("f_cat_2").value;
                cat3 = document.getElementById("f_cat_3").value;
                cat4 = document.getElementById("f_cat_4").value;
                cat5 = document.getElementById("f_cat_5").value;
                address = 'scripts/logfilter.php';
                data="f_cat_1=" + cat1 + "&f_cat_2=" + cat2 + "&f_cat_3=" + cat3 + "&f_cat_4=" + cat4 + "&f_cat_5=" + cat5;
                ajax(address, data, success, failure);
                filt=true;
                refreshFun.stoprefresh();
            }
            else {
                alert("Please select a valid filter.");
            }
        }
    else if (kindof==="clearf")
        {
            miscFun.clearfilt();
            address = 'scripts/updatelog.php';
            ajax(address, '', success, failure);
            filt=false;
            document.getElementById('searchterm').value="Keyword";
            document.getElementById('datesearch').value="Date";
            refreshFun.startrefresh();
        }*/
}
/*
// This function manages the pagentation of the
// log. It receives the desired DB row offset
// which is supplied by readlog.php then sends
// the request to updatelog.php which handles
// the SELECT limits.
function pagentation(pageOffset) {
    success = function()
      {
        miscFun.clearaddedit(); // Clear any open add/edit forms
        
        document.getElementById("refreshed").innerHTML=xmlhttp.responseText;
        
        if (pageOffset <= 0)
        {
            refreshLog('clearf'); // If the page offset returnes to page "1", return to auto refresh
        }
        else
        {
            refreshFun.stoprefresh(); // If on pages > 1, stop refresh
        }
        
        document.documentElement.scrollTop = 0;
      }
    failure = function()
      {
    	if (xmlhttp.readyState===4 && xmlhttp.status===404)
            {
	            document.getElementById("refreshed").innerHTML="";
	            document.location.href = 'index.php';
            }
      }      
    address = 'scripts/updatelog.php';
    data = 'pageOffset=' + pageOffset;
    
    ajax(address, data, success, failure);
}

var addFun = {
    //This function creates the add log form using DOM objects
    //From this form the data is sent to addlog();
    showaddinputs: function() {
        miscFun.clearaddedit();
        var add_form = document.createElement("form");
        var break_it = document.createElement("br");
        
        var title_label = document.createTextNode("Title:");
            add_form.appendChild(title_label);
        
        var title_text = document.createElement("input");
            title_text.id = "add_title";
            title_text.type = "text";
            title_text.size = 60;
            add_form.appendChild(title_text);
            add_form.appendChild(break_it);
        
        var entry_label = document.createTextNode("Entry:");
            add_form.appendChild(entry_label);
            
        var entry_text = document.createElement("textarea");
            entry_text.id = "add_entry";
            entry_text.cols = 60;
            entry_text.rows = 10;
            add_form.appendChild(entry_text);
        
        var cat_label = document.createTextNode("Category:");
            add_form.appendChild(break_it.cloneNode(true));
            add_form.appendChild(cat_label);
            
        var cat_select = document.createElement("select");
            cat_select.id="cat_1";
            cat_select.setAttribute('onchange', 'pop_cat_2(this)');
            
            var icat_1 = document.createElement("option");
                icat_1.value = "select";
                var cat_text = document.createTextNode("Select:");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
            
            icat_1 = document.createElement("option");
                icat_1.value = "Desktop";
                var cat_text = document.createTextNode("Desktop");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
            
            icat_1 = document.createElement("option");
                icat_1.value = "Appliances";
                var cat_text = document.createTextNode("Appliances");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
            
            icat_1 = document.createElement("option");
                icat_1.value = "Network";
                var cat_text = document.createTextNode("Network");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
            
            icat_1 = document.createElement("option");
                icat_1.value = "Servers";
                var cat_text = document.createTextNode("Servers");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
            
            icat_1 = document.createElement("option");
                icat_1.value = "UPS";
                var cat_text = document.createTextNode("UPS");
                icat_1.appendChild(cat_text);
                cat_select.appendChild(icat_1);
                
            add_form.appendChild(cat_select);
            
        cat_select = document.createElement("select");
            cat_select.id="cat_2";
            cat_select.setAttribute('onchange', 'pop_cat_3(this)');
            add_form.appendChild(cat_select);
            
        cat_select = document.createElement("select");
            cat_select.id="cat_3";
            cat_select.setAttribute('onchange', 'pop_cat_4(this)');
            add_form.appendChild(cat_select);
            
        cat_select = document.createElement("select");
            cat_select.id="cat_4";
            cat_select.setAttribute('onchange', 'pop_cat_5(this)');
            add_form.appendChild(cat_select);
            
        cat_select = document.createElement("select");
            cat_select.id="cat_5";
            add_form.appendChild(cat_select);
            
        add_form.appendChild(break_it.cloneNode(true));
            
        var add_button = document.createElement("input");
            add_button.type="button";
            add_button.setAttribute('onclick', 'addFun.addlog();');
            add_button.value="Add Log";
            add_form.appendChild(add_button);
            
        var add_button = document.createElement("input");
            add_button.type="button";
            add_button.setAttribute('onclick', 'miscFun.clearaddedit()');
            add_button.value="Cancel";
            add_form.appendChild(add_button);

        document.getElementById("add_edit").appendChild(add_form);
        
        editing = true;
        document.documentElement.scrollTop = 0;
    },

    //This function sends details for a new log entry to
    //add_log.php. It then refreshes the Live feed.
    addlog :function() {    
        title = document.getElementById("add_title").value;
        title = encodeURIComponent(title);
        entry = document.getElementById("add_entry").value;
        entry = encodeURIComponent(entry);
        cat1 = document.getElementById("cat_1").value;
        cat2 = document.getElementById("cat_2").value;
        cat3 = document.getElementById("cat_3").value;
        cat4 = document.getElementById("cat_4").value;
        cat5 = document.getElementById("cat_5").value;
        
        success=function()
          {
            document.getElementById("add_edit").innerHTML=xmlhttp.responseText;
            clearinput = false;
            editing = false;
            refreshLog("update");
            secleft=120;
          }        
        address = 'scripts/add_log.php';
        data = 'cat_1=' + cat1 + '&cat_2=' + cat2 + '&cat_3=' + cat3 + '&cat_4=' + cat4 + '&cat_5=' + cat5 + '&add_title=' + title + '&add_entry=' + entry;
        
        ajax(address, data, success);
    },
} //addFun

var editFun = {
    //This function displays the fields to edit a log
    showeditinputs: function(log_info) {

        var linfo = eval ('(' + log_info.slice(1, -1) + ')');
        
        miscFun.clearaddedit();
        var add_form = document.createElement("form");
        
        var loguid = document.createElement("input");
            loguid.type="hidden";
            loguid.id="loguid";
            loguid.value="";
            add_form.appendChild(loguid);
            
        var break_it = document.createElement("br");
        
        var title_text = document.createElement("input");
            title_text.id = "edittitle";
            title_text.type = "text";
            title_text.size = 60;
            title_text.value = linfo.title;
            add_form.appendChild(title_text);
			
		add_form.appendChild(break_it);
            
        var entry_text = document.createElement("textarea");
            entry_text.id = "editlog";
            entry_text.cols = 60;
            entry_text.rows = 10;
            var editing_text = document.createTextNode(linfo.entry);
            entry_text.appendChild(editing_text);
            add_form.appendChild(entry_text);
            
		add_form.appendChild(break_it.cloneNode(true));
            
        var cat_label = document.createTextNode("Category: ");
            add_form.appendChild(cat_label);
            
        var acat_label = document.createTextNode(linfo.cat);
            add_form.appendChild(acat_label);
            
        add_form.appendChild(break_it.cloneNode(true));
        add_form.appendChild(break_it.cloneNode(true));
            
        var edit_button = document.createElement("input");
            edit_button.type="button";
            edit_button.setAttribute('onclick', 'editFun.editlogs('+linfo.logid+');');
            edit_button.value="Save Edit";
            add_form.appendChild(edit_button);
            
        edit_button = document.createElement("input");
            edit_button.type="button";
            edit_button.setAttribute('onclick', 'miscFun.clearaddedit();');
            edit_button.value="Cancel";
            add_form.appendChild(edit_button);

        document.getElementById("add_edit").appendChild(add_form);
        
        editing = true;
        document.documentElement.scrollTop = 0;
    },

    //This function is called when a user clicks edit
    //It grabs the information about the log entry they
    //want to edit then calls showeditinputs(); to display
    //the fields.
    grabedit: function(logid) { 
        address = 'scripts/logeditinfo.php';
        data = 'loguid=' + logid;
        success = function()
	        {
              editFun.showeditinputs(xmlhttp.responseText);
	        }
        
        ajax(address, data, success);
    },

    //Sends the finished edited log to a PHP file for processing
    editlogs: function(id) {

        var editedtitle = document.getElementById("edittitle").value;
        editedtitle = encodeURIComponent(editedtitle);
        var editedlog = document.getElementById("editlog").value;
        editedlog = encodeURIComponent(editedlog);

        success = function()
          {
            document.getElementById("add_edit").innerHTML=xmlhttp.responseText;
            clearinput = false;
            editing = false;
            refreshLog("update");
            secleft=120;
          }        
        address = 'scripts/editlogs.php';
        data = 'editlog=' + editedlog + '&edittitle=' + editedtitle + '&choosen=' + id;
        
        ajax(address, data, success)
    },
} //editFun

var searchFun = {
    // Checks if enter key was pressed, if so search
    check: function(e) {
        if (e.keyCode == 13) {
            searchFun.searchlog();
        }
    },

    // Actually searches the database
    searchlog :function() {
    
        var searchfor = document.getElementById('searchterm').value;
        searchfor     = encodeURIComponent(searchfor);
        var datefor   = document.getElementById('datesearch').value;

        if (searchfor!=="" && searchfor!=="Keyword" && searchfor!==null && datefor!=="" && datefor!=="Date" && datefor!==null) {
            type="both";
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
        
        address     = 'scripts/logfilter.php';
        data        = 'keyw=' + searchfor + '&dates=' + datefor + '&type=' + type;
        statechange = function()
	        {
              miscFun.clearaddedit();
              filt=true;
              refreshFun.stoprefresh();
              document.getElementById("refreshed").innerHTML=xmlhttp.responseText;
	        }
        
        ajax(address, data, statechange);
    },
}*/