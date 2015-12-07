<?php

/**
 * This is an example of User Class using PDO
 *
 */

namespace models;
use lib\Core;
use PDO;


class Purshare {

	protected $core;

	function __construct() {
		$this->core = Core::getInstance();
		//$this->core->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	// Get all users
	public function getPurshares($card_id='') {
		$r = array();		
		$where='';
		if ($card_id<>'')
			$where=" WHERE c.id=$card_id ";

		$sql = "SELECT p.*,c.number as card_number,c.serial as card_serial FROM purshares p LEFT OUTER JOIN cards c ON c.id = p.card_id $where ORDER BY p.create_at DESC";
		$stmt = $this->core->dbh->prepare($sql);

		//$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);	

		} else {
			$r = false;
		}		
		
		return $r;
	}

	// Create a new card
	public function create($data) {
		try {
			$sql = "INSERT INTO purshares (create_at, card_id, summa) 
			VALUES (:create_at, :card_id, :summa)";
			$stmt = $this->core->dbh->prepare($sql);
			if ($stmt->execute($data)) {
				$card = new Card ();
				$card->setDateUse($data['card_id'],$data['create_at']);
				return $this->core->dbh->lastInsertId();;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			return $e->getMessage();
		}
		
	}

	public function destroy($id) {
		try {
			
			$sql = "SELECT COUNT(*) FROM purshares WHERE `id`=$id";
			$res=$this->core->dbh->query($sql);
			$r=false;
			if ($res->fetchColumn() == 1) {
				$sql = "DELETE FROM purshares WHERE id=:id";
				$stmt = $this->core->dbh->prepare($sql);
				if ($stmt->execute(array("id"=>$id))) {
					$r=true;
				} 
			}
			return $r;
		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}

	public function destroyByCardId($card_id) {
		try {
			$r=false;
			$sql = "DELETE FROM purshares WHERE card_id=:card_id";
			$stmt = $this->core->dbh->prepare($sql);
			if ($stmt->execute(array("card_id"=>$card_id))) {
				$r=true;
			} 
			return $r;
		} catch(PDOException $e) {
			return $e->getMessage();
		}
		
	}

}