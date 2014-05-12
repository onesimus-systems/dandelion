/**
 *  lQuery javascript library version 1.0.0
 *
 *	This library is released under the GNU General Public License v3
 *
 *	Copyright 2014 Onesimus Computer Systems
 *
 *	March 2014
 */
(function() {
	var lQuery = function(arg) {
		if (!(this instanceof lQuery))
			return new lQuery(arg);
		if (typeof arg === 'undefined')
			arg = '';
		this.lArgs = arg;
	};

	lQuery.fn = lQuery.prototype = {
		init: function() { }
	};

	/**
	 * @brief Handles all ajax requests
	 * 
	 * @author Lee Keitel
	 * 
	 * @param params - An object containing the following keys:
	 * 				*address: The page being requested
	 * 				data:     Any POST/GET data being sent to the address
	 * 				*success: What to do on success, needs to be a function
	 * 				failure:  What to do on failure, needs to be a function, requires knowing what status codes are requested
	 * 				async:    Is the request asyncronis (default: true)
	 * 				type:	  Is the request a GET or POST request
	 * 
	 * 				* Required values
	 */
	lQuery.fn.ajax = function(params) {
		//var	params = this.lArgs;
		// Validate parameters and set defaults if necassary
		if (typeof params !== 'object' || typeof params === 'undefined') {
			return false; // params needs to be an object, if it's not get out!
		}	
		if (typeof params.address === 'undefined' || typeof params.success === 'undefined') {
			return false; // an address and success function are required
		}
		if (typeof params.data === 'undefined') {
			params.data = null; // If no data was given, set it to nothing
		}
		if (typeof params.async === 'undefined') {
			params.async = true; // If async isn't defined, set it to true
		}
		if (typeof params.type === 'undefined') {
			params.type = 'POST'; // If type isn't defined, set it as POST
		}
	
		requestID = "1"; // Set name of the request (I'm too lazy to remove it now, it was part of troubleshooting)

		requestID = new XMLHttpRequest(); // Create the request object
		  
		requestID.onreadystatechange=function() // Assign the statechange function to handle success and failure
			{
				// Prepare variables available to calling functions
				responseText = requestID.responseText;
				ready 		 = requestID.readyState;
				status 		 = requestID.status;
			    if (parseInt(ready)===4 && parseInt(status)===200) // Check if page load was success
			      {
		    		params.success(); // Execute the passed success function
		    		// Help show where this function is being used.
		    		console.log("The lQuery library is depreciated. Please use the jQuery .ajax() method instead.")
			      }
			    else
			      {
			    	if(typeof params.failure !== 'undefined') { // Check if a failure function was defined
			    		params.failure(); // If so, execute it. It's up to the passed function to determine which code it wants to use
			    	}
			      }
			}
		  
		requestID.open(params.type, params.address, params.async); // Open a request with the defined type, address, and async setting
		requestID.setRequestHeader("Content-type","application/x-www-form-urlencoded"); // Declare header type for requests
		requestID.send(params.data); // Send the request!
		return this;
	};

	window._ = lQuery();
	window.lQuery = lQuery();
})();