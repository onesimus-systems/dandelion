var currentID = -1;

function grabNextLevel(parentID) {
	if (parentID == "0:0") { pid = "0:0"; }
	else { pid = parentID.value; }
	params = new Object;
	
	params.address = 'scripts/categories.php';
	params.data = 'parentID='+pid;
	params.success = function()
    {           
          document.getElementById("workArea").innerHTML+=responseText;
          currentID = parentID;
    }
	
	ajax(params);
}