var CategoryManage = {
	currentID: -1,
	currentSelection: [],
	
	grabNextLevel: function(parentID, container) {
		if (parentID == "0:0") { pid = "0:0"; }
		else { pid = parentID.value; }
		
		container = (container == '' || container == null) ? 'categorySelects' : container;
		
		var level = pid.split(':');
		
		if (this.currentSelection[level[1]]) {
			this.currentSelection.splice(level[1]);
		}
		
		this.currentSelection[level[1]] = pid;
		
		if (this.currentSelection.length == 0) {
			this.currentSelection[0] = '0:0';
		}
	
		var params = new Object;	
		params.address = 'scripts/categories.php';
		params.data = 'action=grabcats&parentID='+pid+'&pastSelections='+JSON.stringify(this.currentSelection);
		params.success = function()
	    {
	          //document.getElementsByName(container).item(0).innerHTML=responseText;
			  document.getElementById(container).innerHTML = responseText;
	          CategoryManage.currentID = pid;
	          
	          for (var i=1; i<CategoryManage.currentSelection.length; i++) {
				  document.getElementById('level'+i).value = CategoryManage.currentSelection[i];
			  }
	    }
		params.async = false;
		
		ajax(params);
	},
	
	createNew: function() {
		var catString = this.getCatString();
		var message = 'Add new category\n\n';
		var newCatDesc = '';
		var parent = this.currentSelection[this.currentSelection.length-1].split(':');
		parent = parent[0];
		
		if (this.currentSelection.length == 1) {
			message = 'Create new root category:';
			catString = '';
		}
	    
		while (true) {
			newCatDesc = window.prompt(message+catString);
			
			if (newCatDesc == '') {
				alert('Please enter a category description')
			}
			else if (newCatDesc == null) {
				return false;
			}
			else {
				newCatDesc = encodeURIComponent(newCatDesc);
				break;
			}
		}
		
		var params = new Object;	
		params.address = 'scripts/categories.php';
		params.data = 'action=addcat&parentID='+parent+'&catDesc='+newCatDesc;
		params.success = function()
	    {
	          alert(responseText);
	          CategoryManage.grabNextLevel('0:0');
	    }
		
		ajax(params);
	},
	
	deleteCat: function() {
		var myCatString = this.getCatString();
		var cid = this.currentSelection[this.currentSelection.length-1].split(':');
		cid = cid[0];
	
		if (!window.confirm('Delete '+ myCatString +'?\n\nWarning: All child categories will be moved up one level!')) {
			return false;
		}
		
		var params = new Object;	
		params.address = 'scripts/categories.php';
		params.data = 'action=delcat&cid='+cid;
		params.success = function()
	    {
	          alert(responseText);
	          CategoryManage.grabNextLevel('0:0');
	    }
		
		ajax(params);
	},
	
	getCatString: function() {
		var catString = '';
		
		for (var i=0; i<this.currentSelection.length; i++) {
			if (document.getElementById('level'+(i+1))) {
				var elt = document.getElementById('level'+(i+1));
				//elt = elt.item(0);
				
				/*if (elt.item(elt.selectedIndex).text != 'Select:') {
					catString += elt.item(elt.selectedIndex).text + ':';
				}*/
				if (elt.options[elt.selectedIndex].text != 'Select:') {
					catString += elt.options[elt.selectedIndex].text + ':';
				}
			}
		}
		
		if (catString.length > 0) {
			return catString.substring(0, catString.length - 1) 
		}
		else {
			return false;
		}
	},
};