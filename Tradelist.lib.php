<?php

class Tradelist extends Library {

	private $_mDb = NULL;

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility ( no late static binding )
	 *
	 * @param string $className
	 *        	class name to instantiate
	 *        	
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	private function _Init() {

		if (empty($this->_mDb)) {
			
			$this->_mDb = mysql_connect('localhost', 'c1_radu_usr', 'REDACTED') or die('db connect error');
			
			if (!mysql_select_db('c1_radu')) {
				exit('db select error');
			}
		}
	}

	public function GetTradeItemTypes() {

		$this->_Init();
		
		$data = array();
		
		$q = "SELECT DISTINCT(type) AS item_type FROM r_tradelist";
		
		$rs = mysql_query($q) or die('db query error'. mysql_error());
		
		if (mysql_num_rows($rs)) {
			
			while ($r = mysql_fetch_assoc($rs)) {
				
				$data[] = $r['item_type'];
			}
		}
		
		return $data;
	}
	
	public function GetTradeCategories() {

		$this->_Init();
		
		$data = array();
		
		$q = "SELECT DISTINCT(category) AS category FROM r_tradelist";
		
		$rs = mysql_query($q) or die('db query error'. mysql_error());
		
		if (mysql_num_rows($rs)) {
			
			while ($r = mysql_fetch_assoc($rs)) {
				
				$data[] = $r['category'];
			}
		}
		
		return $data;
	}
	
	public function GetTradeItemsNum($tradeCategory, $tradeType) {
		
		$this->_Init();
		
		if (!empty($tradeCategory) && !empty($tradeType)) {
			
			$q = "SELECT COUNT(*) AS total FROM r_tradelist 
			WHERE category = '{$tradeCategory}' AND type = '{$tradeType}'";
			
			$rs = mysql_query($q) or die('db query error'. mysql_error());
			
			if (mysql_num_rows($rs)) {
				
				return mysql_result($rs, 0, 0);
			}
		}
		
		return FALSE;
	}
}
?>