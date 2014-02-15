<?php
/**
 * @brief Categories is responsible for displaying and managing categories
 *
 * This class displays all categories and manages the addition, deletion,
 * and manipulation of the same.
 *
 * @author Lee Keitel
 * @date February 11, 2014
 ***/
class Categories
{
	private $cat = '';
	private $depth = 0;
	
	function __construct($init = true, $maxdepth = 0) {
		if ($init) {
			$this->init($maxdepth);
		}
	}
	
	private function init($maxdepth) {
		$conn = new dbManage();
		$this->depth = $maxdepth;
		$getCategories = 'SELECT * FROM `category`';
		$this->cat = $cat = $conn->queryDB($getCategories, NULL);
	}
	
	public function showAllCats() {
		if ($this->depth==0) {
			$this->largestBranch();
		}
		$this->makeArrays();
		$this->popArrays();
		$this->genSelect();
		$this->popSelect();
		
	}
	
	public function deleteCats($catString) {
		
	}
	
	private function largestBranch() {
		// Find the deepest node
		foreach ($this->cat as $meow) {
		    if (substr_count($meow['ptree'], ":")+1 > $this->depth) {
		        $this->depth = substr_count($meow['ptree'], ":")+1;
		    }
		}
	}
	
	private function makeArrays() {
		// Create $depth+1 many level arrays
		for ($i = 0; $i < $this->depth+1; $i++) {
		    $this->{'cid'.($i)} = array();
		    $this->{'desc'.($i)} = array();
		    $this->{'ptree'.($i)} = array();
		}
	}
	
	private function popArrays() {
		// Separate arrays by depth
		foreach ($this->cat as $meow) {
		    for ($i = 0; $i < $this->depth+1; $i++) {
		        if ($meow['ptree'] == 0) {
		            array_push($this->desc0, $meow['desc']);
		            array_push($this->cid0, $meow['cid']);
		            array_push($this->ptree0, $meow['ptree']);
		            break;
		        }
		    }
		    for ($i = 0; $i < $this->depth; $i++) {
		        if (substr_count($meow['ptree'], ":") == $i && $meow['ptree'] != 0) {
		            array_push($this->{'desc'.($i+1)}, $meow['desc']);
		            array_push($this->{'cid'.($i+1)}, $meow['cid']);
		            array_push($this->{'ptree'.($i+1)}, $meow['ptree']);
		            break;
		        }
		    }
		}
	}
	
	private function genSelect() {
		// Create the select boxes
		// Populate the first box, assign the right JS function name
		for ($i = 1; $i < $this->depth+2; $i++) {
		    if ($i == 1) {
		        echo '<select name="cat_'.$i.'" id="cat_'.$i.'" onchange="pop_cat_'.($i+1).'(this)">';
		        echo '<option value="select">Select:</option>';
		            foreach ($this->desc0 as $working) {
		                echo '<option value="'.$working.'">'.$working.'</option>';
		            }
		        echo '</select>';
		    }
		    elseif ($i < $this->depth+1) {
		        echo '<select name="cat_'.$i.'" id="cat_'.$i.'" onchange="pop_cat_'.($i+1).'(this)">';
		        echo '</select>';
		    }
		    else {
		        echo '<select name="cat_'.$i.'" id="cat_'.$i.'">';
		        echo '</select>';
		    }
		}
	}
	
	private function popSelect() {
		// Populate the select boxes with categories
		echo '<script type="text/javascript">';
		echo 'var currentCats = \'\';';
		for ($i = 0; $i < $this->depth; $i++) { // For each depth
			// Generate a JS function
		    echo 'function pop_cat_'.($i+2).'(o) {';
		    echo 'd=document.getElementById(\'cat_'.($i+2).'\');';
		    echo 'currentCats += ":" + document.getElementById(\'cat_'.($i+1).'\').value;';
		    echo 'document.getElementById("tester").innerHTML = currentCats;';
		    echo 'if(!d){return;}var mitems=new Array();';
		    
		    for ($h = 0; $h < count($this->{'desc'.($i)}); $h++) { // For each category in level i
		        $item = $this->{'desc'.($i)}[$h];
		        $cid  = $this->{'cid'.($i)}[$h];
		
		        echo 'mitems[\''.$item.'\']=[\'Select:\'';
		        
		        for ($j = 0; $j < count($this->{'desc'.($i+1)}); $j++) { // For each category in level i+1
		            $item = $this->{'desc'.($i+1)}[$j];
		            $pid = $this->{'ptree'.($i+1)}[$j];
		            $pid = array_reverse(explode(":", $pid)); // Separate the parent tree
		            if ($pid[0] == $cid) { // If the parent ID matches the current running level i category, add it to the list
		                echo ',\''.$item.'\'';
		            }
		        }
		        
		        echo '];';
		    }
		    
		    echo 'd.options.length=0;cur=mitems[o.options[o.selectedIndex].value];if(!cur){return;}d.options.length=cur.length;';
		    echo 'for(var i=0;i<cur.length;i++){d.options[i].text=cur[i];d.options[i].value=cur[i];}';
		    echo '}';
		}
		echo '</script>';
	}
}
