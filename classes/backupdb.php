<?php
/**
 * This script creates a backup file of the current database
 *
 * @author Lee Keitel
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

class backupDB
{
    function doBackup($link)
    {
    	$return = '';
    	
    	// Get all of the tables
    	$tables = array();
    	$result = $link->queryDB('SHOW TABLES');
    	
    	foreach($result as $table)
    	{
    		foreach($table as $tablename)
    		{
    			$tables[] = $tablename;
    		}
    	}
    	
    	// Cycle through
    	foreach($tables as $table)
    	{
    		$result = $link->queryDB('SELECT * FROM `'.$table.'`');
    		
    		$stmt = 'SELECT COUNT(*) FROM `'.$table.'`';
    		$num_fields = $link->queryDB($stmt);
    		
    		$return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
    		$row2 = $link->queryDB('SHOW CREATE TABLE `'.$table.'`');
    		$return.= "\n\n".$row2[0]['Create Table'].";\n\n";
    		
    		if (isset($result[0])) {
    			$return.= 'INSERT INTO `'.$table.'` VALUES';
    			
    			foreach ($result as $row)
    			{
    				$return .= '(';
    	
    				foreach($row as $col => $val)
    				{
    					$val = addslashes($val);
    					$val = str_replace("\n","\\n",$val);
    					if (isset($val)) {
    						$return.= is_numeric($val) ? $val : '"'.$val.'"';
    					}
    					else {
    						$return.= '""';
    					}
    					$return.= ', ';
    				}
    				$return = substr($return, 0, -2);
    				$return.= "),\n";
    			}
    			$return = substr($return, 0, -3);
    			$return.=");\n\n\n";
    		}
    	}
    	
    	// Save file
    	if (!is_dir(ROOT.'/backups')) {
    		mkdir(ROOT.'/backups', 0740);
    	}
    	$filepath = ROOT.'/backups/db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
    	file_put_contents($filepath, $return, LOCK_EX);
    	
    	return 'Database Backup Completed';
    }
}