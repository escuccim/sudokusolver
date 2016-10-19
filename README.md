# sudokusolver
A simple PHP Sudoku solver class

Constructor takes in a two dimensional array containing the known values for the Sudoku as follows:
	$array[row][col] = cell value
If value is blank leave the array cell empty or null.

To solve the puzzle call method solve().

Then the object will contain two public variables:
	$sudoku->initArray - contains a copy of the array passed when creating the object, to be used for display purposes
	$sudoku->solutionArray - contains the solved puzzle
