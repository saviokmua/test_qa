<?php

/**
 * This is an example of User Class using PDO
 *
 */

namespace models;
use lib\Core;
use PDO;


class Card {

	protected $core;

	function __construct() {
		$this->core = Core::getInstance();
		//$this->core->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	// Get all users
	public function getCards() {
		$r = array();		
		$where=array();
		$where[]=' WHERE true';
		if (@$_GET['search'])
		{
			foreach (@$_GET['search'] as $key => $value) {
				if ($value<>'')
				{
					switch ($key) {

						case 'number':
						$where[]=" c.number LIKE '%{$value}%'";
						break;

						case 'serial':
						$where[]=" c.serial LIKE '%{$value}%'";
						break;

						case 'date_issue':
						$where[]=" c.date_issue = '{$value}'";
						break;

						case 'date_exp':
						$where[]=" c.date_exp = '{$value}'";
						break;

						case 'status':
						if ($value==0 OR $value==1)
						{
							$where[]=" c.status = {$value}";
						}
						if ($value==2 )
						{
							$where[]="c.date_exp < NOW()";
						}
						break;


						default:
					# code...
						break;
					}
				}
			}

		}

		$sql = "SELECT c.*,cs.name as status_name FROM cards c LEFT OUTER JOIN card_statuses cs ON c.status = cs.id".implode(' AND ',$where);
		$stmt = $this->core->dbh->prepare($sql);

		//$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
			
			foreach ($r as $id => $array) {
				if (strtotime($array['date_exp'])<time())
					$r[$id]['status_name']='expired';
			}
		} else {
			$r = false;
		}		
		
		return $r;
	}

	public function getStatusName($card_id)
	{
		$sql = "SELECT c.*,cs.name as status_name FROM cards c LEFT OUTER JOIN card_statuses cs ON c.status = cs.id AND c.id=$card_id";
		$stmt = $this->core->dbh->prepare($sql);
		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
			if (strtotime($r[0]['date_exp'])<time())
				$r[0]['status_name']='expired';
			$r=$r[0]['status_name'];

		} else {
			$r = false;
		}		
		return $r;
	}

	// Get user by the Id
	public function getCardById($id) {
		$r = array();		
		
		$sql = "SELECT * FROM cards WHERE id=$id ORDER BY number DESC";
		$stmt = $this->core->dbh->prepare($sql);
		//$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
		} else {
			$r = 0;
		}		
		return $r;
	}

	// Create a new card
	public function create($data) {
		try {
			$sql = "INSERT INTO cards (number, serial, date_exp,date_issue) 
			VALUES (:number, :serial, :date_exp,:date_issue)";
			$stmt = $this->core->dbh->prepare($sql);
			$data['date_exp']=date("Y-m-d",mktime(0, 0, 0, date('m'), date('d')+$data['date_exp'], date('Y')));
			$data['date_issue']=date("Y-m-d");
			if ($stmt->execute($data)) {
				return $this->core->dbh->lastInsertId();;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			return $e->getMessage();
		}
		
	}

	public function update($data) {
		try {
			
			if (!@$data['status'])
				$data['status']=0;
			$sql = "UPDATE cards SET number = :number, serial=:serial, ".
			"date_exp=:date_exp, amount=:amount, status=:status".
			" WHERE id=:id";
			$stmt = $this->core->dbh->prepare($sql);
			if ($stmt->execute($data)) {
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			return $e->getMessage();
		}
		
	}

	public function setDateUse($card_id,$date)
	{
		try {		
			if (!empty($card_id)&&!empty($date))
			{
				$sql = "UPDATE cards SET date_use = :date ".
				" WHERE id=:card_id";
				$stmt = $this->core->dbh->prepare($sql);
				if ($stmt->execute(array('card_id'=>$card_id, 'date' => $date))) {
					return true;
				} else {
					return false;
				}
			}

		} catch(PDOException $e) {
			return $e->getMessage();
		}
		

	}

	public function destroy($id) {
		try {
			
			$sql = "SELECT COUNT(*) FROM cards WHERE `id`=$id";
			$res=$this->core->dbh->query($sql);
			$r=false;
			if ($res->fetchColumn() == 1) {
				$sql = "DELETE FROM cards WHERE id=:id";
				$stmt = $this->core->dbh->prepare($sql);
				if ($stmt->execute(array("id"=>$id))) {
					$purshare = new Purshare ();
					$purshare->destroyByCardId($id);
					$r=true;
				} 
			}
			return $r;
		} catch(PDOException $e) {
			return $e->getMessage();
		}
		
	}

	public function isNumber($number)
	{
		$sql = "SELECT COUNT(*) FROM cards WHERE `number`='$number'";
		$res=$this->core->dbh->query($sql);
		$r=true;
		if ($res->fetchColumn() > 0) {
			$r=false;
		}
		return $r;
	}

	public function isSerial($serial)
	{
		$sql = "SELECT COUNT(*) FROM cards WHERE `serial`='$serial'";
		$res=$this->core->dbh->query($sql);
		$r=true;
		if ($res->fetchColumn() > 0) {
			$r=false;
		}
		return $r;
	}

}