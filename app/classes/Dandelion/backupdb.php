<?php
/**
  * Backsup the database to a file
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  * The full GPLv3 license is available in LICENSE.md in the root.
  *
  * @author Lee Keitel
  * @date March 2014
***/
namespace Dandelion;

use \Dandelion\Storage\MySqlDatabase;

/**
 * Not much to say here. It gets a list of all the tables then goes
 * through each one and gets all the rows of data then writes it to
 * a file with the proper SQL instructions to import back to MySQL.
 */
class backupdb
{
    public function __construct(MySqlDatabase $db) {
      $this->db = $db;
    }
    /**
     * Perform backup
     *
     * @return string
     */
    public function doBackup()
    {
        $return = '';

        // Get all of the tables
        $tables = array();
        $result = $this->db->raw('SHOW TABLES')->go();

        foreach($result as $table) {
            foreach($table as $tablename) {
                $tables[] = $tablename;
            }
        }

        // Cycle through
        foreach($tables as $table) {
            $result = $this->db->select()->from($table)->get();

            $return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
            $row2 = $this->db->raw('SHOW CREATE TABLE '.$table)->go();
            $return.= "\n\n".$row2[0]['Create Table'].";\n\n";

            if (isset($result[0])) {
                $return.= 'INSERT INTO `'.$table.'` VALUES';

                foreach ($result as $row) {
                    $return .= '(';

                    foreach($row as $col => $val) {
                        $val = addslashes($val);
                        $val = str_replace("\n","\\n",$val);
                        if (isset($val)) {
                            $return.= is_numeric($val) ? $val : '"'.$val.'"';
                        } else {
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
