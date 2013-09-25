<?php

//error_reporting(0);
class MySQL{
   var $Host = C_DB_HOST;
   var $Database = C_DB_NAME;
   var $User = C_DB_USER;
   var $Password = C_DB_PASS;
   var $Link_ID = 0;
   var $Query_ID = 0;
   var $Record   = array();
   var $Row;
   var $Errno = 0;
   var $Error = "";

   function halt($msg){
	 //report_error("Database Error", $this->Errno ($this->Error));
	 //die("Session halted.");
    }

   function connect(){
      if($this->Link_ID == 0){
         $this->Link_ID = mysql_connect($this->Host, $this->User, $this->Password);
         if (!$this->Link_ID){
            $this->halt("Database connection failure");
         }
         $SelectResult = mysql_select_db($this->Database, $this->Link_ID);
         if(!$SelectResult){
            $this->Errno = mysql_errno($this->Link_ID);
            $this->Error = mysql_error($this->Link_ID);
            $this->halt("Database not found: <i>".$this->Database."</i>");
         }
      }
   }

   function query($Query_String){
      $this->connect();
      $this->Query_ID = mysql_query($Query_String,$this->Link_ID);
      $this->Row = 0;
      $this->Errno = mysql_errno();
      $this->Error = mysql_error();
      if (!$this->Query_ID){
         $this->halt("SQL Error: <i>".$Query_String."</i>");
      }
      return $this->Query_ID;
   }

   function update($table, $params, $conditions = false){
	$cols = $this->query("describe ".$table);
	$qpart = "";
	while ($col = mysql_fetch_array($cols)){
		if (isset($params[$col[0]]) && $col[0] != "id"){
			$val = $params[$col[0]];
			if ($qpart != ""){
				$qpart .= ", ";
			}
			$qpart .= $col[0]." = ";
			if ((is_numeric($val) && $val[0] != "0") || ((strpos($val, "+") || strpos($val, "-")) && substr($val, 0, strlen($col[0])) == $col[0])){
				$qpart .= $val;
			}
			else{
				$val = str_replace("'", "''", $val);
				//$val = str_replace("'''", "''", $val);
				$qpart .= "'".$val."'";
			}
		}
	}
	if (isset($params["id"]))
		if (!is_numeric($params["id"]) || $params["id"][0] == "0")
			$params["id"] = "'".$params["id"]."'";
	if ($conditions == false)
		$sql = "UPDATE ".$table." SET ".$qpart." WHERE id = ".$params["id"];
	else
		$sql = "UPDATE ".$table." SET ".$qpart." WHERE ".$conditions;
	//echo $sql;
	$this->query($sql);
   }

   function insert($table, $params){
	$cols = $this->query("describe ".$table);
	$qpart1 = "";
	$qpart2 = "";
	while ($col = mysql_fetch_array($cols)){
		if (isset($params[$col[0]])){
			$val = $params[$col[0]];
			if ($qpart1 != ""){
				$qpart1 .= ", ";
				$qpart2 .= ", ";
			}
			$qpart1 .= $col[0];
			if (is_numeric($val) && $val[0] != "0"){//substr($col[1], 0, 3) == "var"){
				$qpart2 .= $val;
			}
			else{
				$val = str_replace("'", "''", $val);
				//$val = str_replace("'''", "''", $val);
				$qpart2 .= "'".$val."'";
			}
		}
	}
	$sql = "INSERT INTO ".$table."(".$qpart1.") VALUES(".$qpart2.")";
	//echo $sql;
	$this->query($sql);
   }

   function delete($table, $id, $conditions = false){
	if ($conditions == false)
		$sql = "DELETE FROM ".$table." WHERE id = ".$id;
	else
		$sql = "DELETE FROM ".$table." WHERE ".$conditions;
	//echo $sql;
	$this->query($sql);
   }

   function next_record(){
      $this->Record = mysql_fetch_array($this->Query_ID);
      $this->Row += 1;
      $this->Errno = mysql_errno();
      $this->Error = mysql_error();
      $stat = is_array($this->Record);
      if (!$stat){
         mysql_free_result($this->Query_ID);
         $this->Query_ID = 0;
      }
      return $this->Record;
   }

   function num_rows(){
      return mysql_num_rows($this->Query_ID);
   }

   function close(){
      if($this->Link_ID != 0){
         mysql_close($this->Link_ID);
      }
   }
   
   function insert_id(){
      return mysql_insert_id($this->Link_ID);
   }
   
   function affected_rows(){
      return mysql_affected_rows($this->Link_ID);
   }
}

?>
