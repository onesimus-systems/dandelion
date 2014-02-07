<?php
/*
 * Lee Keitel
 * January 27, 2014
 *
 * This page handles the login function of Dandelion.
 * It takes the credentials from the index page,
 * checks if the person's a user, and if so checks for
 * an existing session token (and deletes if found),
 * then creates a new session token, stores it in the
 * DB and directs the user as needed.
*/

include_once 'dbconnect.php'; // Required for access to DB

echo "<p>Logging in...</p>"; // In case it take a moment, rarely if ever seen

// Declare and clear variables for login info
// Just to make sure
$username = $plain_word = "";

// Was there a login attempt? If so grab the data and check login
if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Connect to DB
        $conn = new dbManage();
        
        $username = $_POST["in_name"];
        $plain_word = $_POST["in_pass"];
        // Begin login procedure
        isuser($username, $plain_word, $conn, $cookie_name);
    }

// Determines if the person is a user or not
// If yes, validates and redirects to viewlog.php
// If no, yells at user, loudly
function isuser($uname, $pword, $conn, $cookie_name) {

    // First, is this person even a user?
    
    $stmt = 'SELECT * FROM users WHERE username = :user';
    $param = array('user' => $uname);
    
    $sel_user = $conn->queryDB($stmt, $param);

    if ($sel_user[0]['password']) { // Check if password is correct
        $goodToGo = password_verify($pword, $sel_user[0]['password']);
    }
    else {
        $goodToGo = false;
    }
    
    // So they are!! Well, make a token for them!
	if ($goodToGo) {
		beginSess($sel_user[0]['userid'], $conn, $cookie_name);
        
		$_SESSION['userInfo'] = $sel_user[0];
        
        echo 'Logged in. Please wait as I redirect you...';
        
        // Make this into switch statement
        if ($sel_user[0]['firsttime'] == 1) {
            header ( 'Location: ../tutorial.phtml' );
        }
		elseif ($sel_user[0]['firsttime'] == 2) {
            header ( 'Location: ../reset.phtml' );
        }
		elseif ($sel_user[0]['firsttime'] == 3) {
            header ( 'Location: ../whatsnew.phtml' );
        }
        else {
            header( 'Location: ../viewlog.phtml' );
        }
	}
	else { // Sadly they have failed. Walk the plank!
        $_SESSION['badlogin'] = true; // Used to display a message to the user
		header( 'Location: ../index.php' );
	}
}

// Generate a token, put it in the DB and as a cookie for auth
function beginSess($user, $conn, $cookie_name) {
    $token = openssl_random_pseudo_bytes(128);
    $token = bin2hex($token);
	$extime = time()+60*60*20;
    
    $stmt = 'SELECT * FROM session_token WHERE userid = :id';
    $param = array('id' => $user);
    
    $auth_user = $conn->queryDB($stmt, $param);
	
	// If there is a token, delete it before setting new one
	if ($auth_user[0]['session_id']) {
        
        $stmt = 'DELETE FROM session_token WHERE userid = :id';
        $param = array('id' => $user);
        
        $conn->queryDB($stmt, $param);
	}
	
    // Set session token
    
    $stmt = 'INSERT INTO session_token (token, userid, expire) VALUES(:sshtoken,:uid,:exp)';
    $param = array(
        'sshtoken' => $token,
        'uid' => $user,
        'exp' => $extime
    );
    
    $conn->queryDB($stmt, $param);
    
    // Set cookie for token
    $cookie = $user . ':' . $token;
    $mac = hash_hmac('sha256', $cookie, "usi.edu");
    $cookie .= ':' . $mac;
    setcookie($cookie_name, $cookie, 0, "/");
    $_SESSION['loggedin'] = true;
    echo "Cookie Set";
}