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
    //Gets and formats the current Unix Epoch time and returns value
    microtime: function(get_as_float) {
      var now = new Date().getTime() / 1000;
      var s = parseInt(now, 10);

      return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
    },

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
        secleft=120;
        //document.getElementById("rcounter").innerHTML = "2:01";
        refreshc = setInterval(function() {refreshFun.rcounterc()}, 1000);
        wherearewe = setInterval(function() {presence.checkstat(0)}, 30000);
        autore = true;
        refreshLog("update");
        refreshFun.refreshb();
    },

    //This functions shows the refresh clock and initiates a refresh
    //after 2 minutes.
    rcounterc: function() {        
        if (secleft > 0) {
            secleft = secleft - 1;
            }
        else {
            secleft=120;
            refreshLog("update");
            }
    },

    //This function displays the appropriate refresh button
    refreshb: function() {
        document.getElementById("refreshbutton").innerHTML = autore ? '<input type="button" class="dButton" value="Stop Auto Refresh" onClick="refreshFun.stoprefresh();" /> Autorefresh: On' : '<input type="button" class="dButton" value="Start Auto Refresh" onClick="refreshFun.startrefresh();" /> Autorefresh: Off ';
    },

    //Stops auto refresh
    stoprefresh: function() {
        clearInterval(refreshc);
        autore = false;
        document.getElementById("rcounter").innerHTML = "";
        refreshFun.refreshb();
    },
} //refreshFun

//This function updates the live feed.
//If kindof == "update" it shows the recent log entries
//If kindof == "filter" it sends the filter details to
//logfilter.php and shows the returned output.
function refreshLog(kindof) {    
    var start=miscFun.microtime(true);
    
    window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      
    xmlhttp.onreadystatechange=function()
      {
          if (xmlhttp.readyState===4 && xmlhttp.status===200)
            {
                var end=miscFun.microtime(true);
                var distime=end-start;
                
                document.getElementById("refreshed").innerHTML=xmlhttp.responseText;
                
                presence.checkstat(0);
                
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
          else if (xmlhttp.readyState===4 && xmlhttp.status===404)
            {
                document.getElementById("refreshed").innerHTML="";
                document.location.href = 'index.php';
            }
      }
    
    if (kindof==="update" && !filt)
        {
            xmlhttp.open("POST",'scripts/updatelog.php',true);
            xmlhttp.send();
        }
    else if (kindof==="filter")
        {
            cat1 = document.getElementById("f_cat_1").value;
            
            if (cat1 != null && cat1 != "" && cat1 != "select") {
                cat2 = document.getElementById("f_cat_2").value;
                cat3 = document.getElementById("f_cat_3").value;
                cat4 = document.getElementById("f_cat_4").value;
                cat5 = document.getElementById("f_cat_5").value;
                xmlhttp.open("POST",'scripts/logfilter.php',true);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("f_cat_1=" + cat1 + "&f_cat_2=" + cat2 + "&f_cat_3=" + cat3 + "&f_cat_4=" + cat4 + "&f_cat_5=" + cat5);
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
            xmlhttp.open("POST",'scripts/updatelog.php',true);
            xmlhttp.send();
            filt=false;
            document.getElementById('searchterm').value="Keyword";
            document.getElementById('datesearch').value="Date";
            refreshFun.startrefresh();
        }
}

// This function manages the pagentation of the
// log. It receives the desired DB row offset
// which is supplied by readlog.php then sends
// the request to updatelog.php which handles
// the SELECT limits.
function pagentation(pageOffset) {
    window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      
    xmlhttp.onreadystatechange=function()
      {
          if (xmlhttp.readyState===4 && xmlhttp.status===200)
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
          else if (xmlhttp.readyState===4 && xmlhttp.status===404)
            {
                document.getElementById("refreshed").innerHTML="<span class=\"bad\">Error communicating with server. Please <a href=\"index.php\">log in again</a></span>";
            }
      }
      
    xmlhttp.open("POST",'scripts/updatelog.php',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("pageOffset=" + pageOffset);
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
        
        window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    document.getElementById("add_edit").innerHTML=xmlhttp.responseText;
                    clearinput = false;
                    editing = false;
                    refreshLog("update");
                    secleft=120;
                }
          }
        
        xmlhttp.open("POST",'scripts/add_log.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("cat_1=" + cat1 + "&cat_2=" + cat2 + "&cat_3=" + cat3 + "&cat_4=" + cat4 + "&cat_5=" + cat5 + "&add_title=" + title + "&add_entry=" + entry);
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
        window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    editFun.showeditinputs(xmlhttp.responseText);
                }
          }
        
        xmlhttp.open("POST",'scripts/logeditinfo.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("loguid=" + logid);
    },

    //Sends the finished edited log to a PHP file for processing
    editlogs: function(id) {

        var editedtitle = document.getElementById("edittitle").value;
        editedtitle = encodeURIComponent(editedtitle);
        var editedlog = document.getElementById("editlog").value;
        editedlog = encodeURIComponent(editedlog);
        
        window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    document.getElementById("add_edit").innerHTML=xmlhttp.responseText;
                    clearinput = false;
                    editing = false;
                    refreshLog("update");
                    secleft=120;
                }
          }
        
        xmlhttp.open("POST",'scripts/editlogs.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("editlog=" + editedlog + "&edittitle=" + editedtitle + "&choosen=" + id);
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
        searchfor = encodeURIComponent(searchfor);
        var datefor = document.getElementById('datesearch').value;

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
        
        window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          
        xmlhttp.onreadystatechange=function()
          {
              if (xmlhttp.readyState===4 && xmlhttp.status===200)
                {
                    miscFun.clearaddedit();
                    filt=true;
                    refreshFun.stoprefresh();
                    document.getElementById("refreshed").innerHTML=xmlhttp.responseText;
                }
          }
        
        xmlhttp.open("POST",'scripts/logfilter.php',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("keyw=" + searchfor + "&dates=" + datefor + "&type=" + type);
    },
}