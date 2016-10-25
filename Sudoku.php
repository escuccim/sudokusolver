<?php

namespace App;


class Sudoku {
	var $valueArray;
	var $solutionArray;
	var $initArray;
	var $action;
	var $solved;

	function __construct($action, $data = null){
		$this->solutionArray = $this->initializeSolutionArray();
		$this->valueArray = $this->initializeValueArray();
		$this->initArray = $this->valueArray;
		$this->action = $action;
		$this->solved = 0;
		
		if(isset($data)) {
			$this->valueArray = $data;
			$this->initArray = $data;
		}
		if(!$this->checkData()){
			$this->action = 'blank';
		}
	}

	public function solve(){
		// initialize solutionArray to ValueArray
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
					
				if ($this->valueArray[$row][$col] != '') {
					$this->setSquare($row, $col, $this->valueArray[$row][$col]);
				}
					
			}
		}

		// iterations to try to solve
		for($n = 1; $n < 20; $n++){

			// check squares
			for($row = 0; $row < 9; $row++){
				for($col = 0; $col < 9; $col++){
					$this->checkSquare($row, $col);
				}
					
			}
			// check rows + columns
			for($row = 0; $row < 9; $row++){
				$this->checkColumn($row);
				$this->checkRow($row);
				$this->checkQuadrant($row+1);
			}

			// check if solved
			if($this->checkIfSolved() == 1){
				//echo "SOLVED on Iteration " . $n . "!<BR>";
				$this->solved = 1;
				break;
			}
			// if solved break
		}

		//outputStatus($solutionArray);
		$this->updateSolution();
	}

	private function scrubData($array){
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				$num = (int) $array[$row][$col];
				if($num < 1 || $num > 9){
					$num = '';
				}
				$array[$row][$col] = $num;
			}
		}

		return $array;
	}

	protected function setSquare($r, $c, $v){

		// set square value
		$this->solutionArray[$r][$c][0] = $v;

		// set possible values properly
		for($i = 1; $i < 10; $i++){
			if($i != $v) {
				$this->solutionArray[$r][$c][$i] = 0;
			}
		}

		$this->updateRow($r, $c, $v);
		$this->updateColumn($r, $c, $v);
		$this->updateQuadrant($r, $c, $v);

	}

	protected function updateRow($r, $c, $v){

		for($col = 0; $col < 9; $col++){
			if($col != $c)
				$this->solutionArray[$r][$col][$v] = 0;
		}
	}

	protected function updateColumn($r, $c, $v){
		for($row = 0; $row < 9; $row++){
			if($row != $r)
				$this->solutionArray[$row][$c][$v] = 0;
		}
	}

	protected function updateQuadrant($r, $c, $v){

		// set which quadrant
		if($r < 3){
			$startR = 0;
			$endR = 2;
		} elseif($r < 6) {
			$startR = 3;
			$endR = 5;
		} elseif($r < 9){
			$startR = 6;
			$endR = 8;
		}

		if($c < 3){
			$startC = 0;
			$endC = 2;
		} elseif($c < 6){
			$startC = 3;
			$endC = 5;
		} elseif($c < 9){
			$startC = 6;
			$endC = 8;
		}

		// loop through quadrant
		for($row = $startR; $row <= $endR; $row++){
			for($col = $startC; $col <= $endC; $col++){
				if( ($row != $r) && ($col != $c) ) {
					$this->solutionArray[$row][$col][$v] = 0;
				}
			}
		}
	}

	protected function checkSquare($r, $c){

		if($this->solutionArray[$r][$c][0] == 0){
			$totalOptions = 0;
			$selectedValue = 0;

			for($i = 1; $i < 10; $i++){
				$totalOptions += $this->solutionArray[$r][$c][$i];
				if($this->solutionArray[$r][$c][$i] == 1)
					$selectedValue = $i;
			}

			if($totalOptions == 1){
				$this->setSquare($r, $c, $selectedValue);
			}
		}
	}

	protected function checkColumn($c){
		// loop through digits
		for($i = 1; $i < 10; $i++){
			// check if that number is solved for already
			$numberSolvedFor = 0;
			for($row = 0; $row < 9; $row++){
				if($this->solutionArray[$row][$c][0] == $i){
					$numberSolvedFor = 1;
					break;
				}
			}
			// if not solved for
			if($numberSolvedFor == 0){
				$totalOptions = 0;
				$selectedValue = 0;
				$rowToSet = 11;

				for($row = 0; $row < 9; $row++){
					$totalOptions += $this->solutionArray[$row][$c][$i];
					if($this->solutionArray[$row][$c][$i] == 1){
						$selectedValue = $i;
						$rowToSet = $row;
					}
				}

				if($totalOptions == 1){
					$this->setSquare($rowToSet, $c, $selectedValue);
				}
			}
		}
	}

	protected function checkRow($r){
		// loop through digits
		for($i = 1; $i < 10; $i++){
			// check if that number is solved for already
			$numberSolvedFor = 0;
			for($col = 0; $col < 9; $col++){
				if($this->solutionArray[$r][$col][0] == $i){
					$numberSolvedFor = 1;
					break;
				}
			}
			// if not solved for
			if($numberSolvedFor == 0){
				$totalOptions = 0;
				$selectedValue = 0;
				$colToSet = 11;
					
				for($col = 0; $col < 9; $col++){
					$totalOptions += $this->solutionArray[$r][$col][$i];
					if($this->solutionArray[$r][$col][$i] == 1){
						$selectedValue = $i;
						$colToSet = $col;
					}
				}
					
				if($totalOptions == 1){
					$this->setSquare($r, $colToSet, $selectedValue);
				}
			}
		}
	}

	protected function checkQuadrant($q){
		// set col and row params for quadrant
		if($q <= 3){
			$startRow = 0;
			$endRow = 2;
		} elseif($q <= 6){
			$startRow = 3;
			$endRow = 5;
		} else {
			$startRow = 6;
			$endRow = 8;
		}
		if($q % 3 == 1 ) {
			$startCol = 0;
			$endCol = 2;
		} elseif($q % 3 == 2){
			$startCol = 3;
			$endCol = 5;
		} else {
			$startCol = 6;
			$endCol = 8;
		}

		// loop through digits
		for($i = 1; $i < 10; $i++){
			// check if that number is solved for already
			$numberSolvedFor = 0;
			for($col = $startCol; $col <= $endCol; $col++){
				for($row = $startRow; $row <= $endRow; $row++){
					if($this->solutionArray[$row][$col][0] == $i){
						$numberSolvedFor = 1;
						break;
					}
				}
			}
			// if not solved for
			if($numberSolvedFor == 0){
				$totalOptions = 0;
				$selectedValue = 0;
				$colToSet = 11;

				for($col = $startCol; $col <= $endCol; $col++){
					for($row = $startRow; $row <= $endRow; $row++){
						$totalOptions += $this->solutionArray[$row][$col][$i];
						if($this->solutionArray[$row][$col][$i] == 1){
							$selectedValue = $i;
							$colToSet = $col;
							$rowToSet = $row;
						}
					}
				}

				if($totalOptions == 1){
					$this->setSquare($rowToSet, $colToSet, $selectedValue);
				}
			}

		}
	}

	protected function checkIfSolved(){
		$solved = 1;
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				if($this->solutionArray[$row][$col][0] == 0){
					$solved = 0;
					break;
				}
			}
		}
		return $solved;
	}

	protected function updateSolution(){
		// update value array and check it
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				if($this->solutionArray[$row][$col][0] != 0)
					$this->valueArray[$row][$col] = $this->solutionArray[$row][$col][0];
			}
		}
	}

	protected function initializeSolutionArray(){
		// initialize solutionArray
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				$solutionArray[$row][$col][0] = 0;
				for($i = 1; $i < 10; $i++){
					$solutionArray[$row][$col][$i] = 1;
				}
			}
		}
		return $solutionArray;
	}

	protected function initializeValueArray(){
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				$valueArray[$row][$col] = '';
			}
		}
		return $valueArray;
	}
	
	protected function checkData(){
		$dataFound = 0;
		for($row = 0; $row < 9; $row++){
			for($col = 0; $col < 9; $col++){
				if($this->valueArray[$row][$col]){
					$dataFound = 1;
					break;
				}
			}
		}
		return $dataFound;
	}
}