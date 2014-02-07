<?php
include_once 'dbconnect.php';

// Authenticate user, if fail go to login page
if (checkLogIn()) {
	if ($_SESSION['userInfo'][5] === "admin") {
		$admin_link = '| <a href="admin.php">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($_SESSION['userInfo'][5] !== "guest") {
		$settings_link = '| <a href="settings.php">Settings</a>';
	}
	else {
		$settings_link = '';
	}
}
else {
	header( 'Location: index.php' );
}

// Connect to DB
$db = new DB();
$conn = $db->dbConnect();

try {
    $getCategories = $conn->prepare('SELECT * FROM `category`');
    $getCategories->execute();
} catch(PDOException $e) {
    echo 'Database error';
}

/*
echo '<pre>';
print_r($getCategories->fetchall(PDO::FETCH_ASSOC));
echo '</pre>';*/

$cat = $getCategories->fetchall(PDO::FETCH_ASSOC);
$depth = isset($_POST['maxdepth']) ? $_POST['maxdepth'] : 0;
//$parent = isset($_POST['parent']) ? $_POST['parent'] : '';

// Find the deepest node
// May user this at a later date but for
// now it was mainly as an exercise

foreach ($cat as $meow) {
    if (substr_count($meow['ptree'], ":")+1 > $depth) {
        $depth = substr_count($meow['ptree'], ":")+1;
    }
}

// Create $depth+1 many level arrays
for ($i = 0; $i < $depth+1; $i++) {
    ${'cid'.($i)} = array();
    ${'desc'.($i)} = array();
    ${'ptree'.($i)} = array();
}

// Separate arrays by depth
foreach ($cat as $meow) {
    for ($i = 0; $i < $depth+1; $i++) {
        if ($meow['ptree'] == 0) {
            array_push($desc0, $meow['desc']);
            array_push($cid0, $meow['cid']);
            array_push($ptree0, $meow['ptree']);
            break;
        }
    }
    for ($i = 0; $i < $depth; $i++) {
        if (substr_count($meow['ptree'], ":") == $i && $meow['ptree'] != 0) {
            array_push(${'desc'.($i+1)}, $meow['desc']);
            array_push(${'cid'.($i+1)}, $meow['cid']);
            array_push(${'ptree'.($i+1)}, $meow['ptree']);
            break;
        }
    }
}

for ($i = 1; $i < $depth+2; $i++) {
    if ($i == 1) {
        echo '<select name="cat_'.$i.'" id="cat_'.$i.'" onchange="pop_cat_'.($i+1).'(this)">';
        echo '<option value="select">Select:</option>';
            foreach ($desc0 as $working) {
                echo '<option value="'.$working.'">'.$working.'</option>';
            }
        echo '</select>';
    }
    elseif ($i < $depth+1) {
        echo '<select name="cat_'.$i.'" id="cat_'.$i.'" onchange="pop_cat_'.($i+1).'(this)">';
        echo '</select>';
    }
    else {
        echo '<select name="cat_'.$i.'" id="cat_'.$i.'">';
        echo '</select>';
    }
}

echo '<script type="text/javascript">';
for ($i = 0; $i < $depth; $i++) {
    echo 'function pop_cat_'.($i+2).'(o) {';
    echo 'd=document.getElementById(\'cat_'.($i+2).'\');';
    echo 'if(!d){return;}var mitems=new Array();';
    
    for ($h = 0; $h < count(${'desc'.($i)}); $h++) {
        $item = ${'desc'.($i)}[$h];
        $cid  = ${'cid'.($i)}[$h];
        
        echo 'mitems[\''.$item.'\']=[\'Select:\'';
        
        for ($j = 0; $j < count(${'desc'.($i+1)}); $j++) {
            $item = ${'desc'.($i+1)}[$j];
            $pid = ${'ptree'.($i+1)}[$j];
            $pid = array_reverse(explode(":", $pid));
            if ($pid[0] == $cid) {
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
/*
?>
<script type="text/javascript">
    //if(!d){return;}			
    //var mitems=new Array();
    mitems['Desktop']=[''];
    mitems['Appliances']=['Select:','Struxureware','bomgar','campusmgr','copysense','dr4000','fish1','fish2','hr_ibm6400','kace','procerastat','wlc','wlse'];
    mitems['Network']=['Select:','MonitorLogs','Nexus1','Nexus2','admin','housing','ilight','labs','power','storage','unix'];
    mitems['Servers']=['Select:','novell','unix','VMservers','windows'];
    mitems['UPS']=[''];
    //echo 'd.options.length=0;cur=mitems[o.options[o.selectedIndex].value];if(!cur){return;}d.options.length=cur.length;';
    //echo 'for(var i=0;i<cur.length;i++){d.options[i].text=cur[i];d.options[i].value=cur[i];}';
</script>
<?php*/

/*
// Populate select boxes with categories
echo '<form>';

for ($i = 0; $i < $depth+1; $i++) {
    $turkey =  isset(${'desc'.($i)}[0]) ? ${'desc'.($i)}[0] : ''; // Don't ask
    $ham =     isset(${'cid'.($i)}[0]) ? ${'cid'.($i)}[0] : ''; // Don't ask
    $chicken = isset(${'ptree'.($i)}[0]) ? ${'ptree'.($i)}[0] : ''; // Don't ask
    
    $tree = explode(":", $chicken);
    $tree = array_reverse($tree);
    
    if ($depth == 0) {
        echo '<select onChange="showCats('.($i+1). ', this.value)">';
        foreach ($desc0 as $cat) {
            echo '<option value='.${'cid'.($i)}[$i].'>'.$cat.'</option>';
        }
        echo '</select>';
    }
    
    if ($turkey != '' && $turkey != NULL && $depth != 0) {
        echo '<select onChange="showCats('.($i+1). ', this.value)">';
        foreach (${'desc'.($i)} as $cat) {
            if ($cat == $parent) {
            echo '<option value='.${'cid'.($i)}[$i].'>'.$cat.'</option>'; }
        }
        echo '</select>';
    }
}
echo $parent;
echo '</form>';
*/
?>