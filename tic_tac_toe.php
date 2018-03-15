<?php
/*
 * Please fix each todo in this file in order.
 * Starting from the top.
 * When you are finished and run it, the tests should pass and you should be able to play.
 *
 * If you find other bugs, fix them as well.
 */


if (PHP_SAPI !== 'cli')
{
    echo 'You must run this script from console! Try: php tic_tac_quiz.php';
    exit;
}


/**
 * Keeps track of pieces
 */
class Board
{
    const ROWS = 3;
    const COLUMNS = 3;

    /** @var string[][] */
    private $pieces;

    /**
     * @param string[][] $pieces
     */
    public function __construct($pieces = [])
    {
        $this->pieces = $this->fillBlanks($pieces);
    }

    /**
     * A visual representation of the game board
     * @return string
     */
    function __toString()
    {
        $string = '';
	$i=0;
        foreach ($this->pieces as $column) {

            foreach ($column as $piece) {
                if ($piece === '') {
                    $string .= ('-');
                } else {
                    $string .= $piece;
                }
		$i++;
		if($i%3==0)
		{
			$string .= "\n";
		}
            }

        }

        return $string;
    }

    /**
     * @return bool True when there are no more empty spaces
     */
    public function isFull()
    {
        foreach ($this->pieces as $columns) {
            foreach ($columns as $piece) {
                if ($piece === '') {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * @return array Array of all possible positions [x,y]...
     */
    private function getAllPositions()
    {
        $positions = [];

        for ($y = 0; $y < self::ROWS; $y++) {
            for ($x = 0; $x < self::COLUMNS; $x++) {

                $positions[] = [$y, $x];
            }
        }

        return $positions;
    }

    /**
     * Define each possible row/column combination with an empty string ''
     * @param string[][] $pieces
     * @return string[][]
     */
    private function fillBlanks(array $pieces)
    {
        foreach ($this->getAllPositions() as list($y, $x)) {

            if (!isset($pieces[$y][$x])) {
                $pieces[$y][$x] = '';
            }
        }

        return $pieces;
    }

    /**
     * Todo
     * Return a list of positions (an array of [y, x] pairs) that have no piece (where the value is '')
     * @return int[][] A list of blank positions, [x, y]...
     */
    public function getBlankPositions()
    {
//        example
        $blanks = [
            [0, 0],
            [0, 1],
            [0, 2],
        ];

//        todo...

        for($i=0;$i<self::ROWS;$i++)
	{
		for($j=0;$j<self::COLUMNS;$j++)
		{
			if($this->pieces[$i][$j] == '')
			{
				 array_push($blanks, [$i,$j]);
			}
		}
	}

        return $blanks;
    }

    /**
     * @param int $y Row
     * @param int $x Column
     * @param string $piece Piece, 'O' or 'X'
     * @return Board
     */
    public function addPiece($y, $x, $piece)
    {
        $this->pieces[$y][$x] = $piece;

        return $this;
    }

    /***
    * returns X if AI won
    * returns O if player won
    **/
    public function getWinner()
    {
//        todo
        $p = $this->pieces;
        $i=0;
        //check the rows for winner
        for($i=0;$i<self::ROWS;$i++)
        {
                if(($p[$i][0] == $p[$i][1]) && ($p[$i][1] == $p[$i][2]) && $p[$i][0] != '')
                {
                        if($p[$i][0] == 'O')
			{
				return 'O';
			}
                        else
			{
				return 'X';
			}
                }
        }

        //check the columns for winner
        for($j=0;$j<self::ROWS;$j++)
        {
                if(($p[0][$j] == $p[1][$j]) && ($p[1][$j] == $p[2][$j]) &&   $p[0][$j] != '')
                {
                        if($p[0][$j] == 'O')
                	{
				return 'O';
			}
                        else
			{
                       		return 'X';

			}
                }

        }

        //check the diagonals for winner
        if(($p[0][0]==$p[1][1] && $p[1][1] == $p[2][2] && $p[0][0] != '') || ($p[0][2] == $p[1][1] && $p[1][1] == $p[2][0] && $p[0][2] != ''))
        {
                if($p[1][1] == 'O')
		{
			return 'O';
		}
                else
		{
               		return 'X';

		}
        }
        return -1; //return -1, keep playing
    }

    /**
     * @param int $y Row
     * @param int $x Column
     * @return string
     */
    public function getPieceAtPosition($y, $x)
    {
        return $this->pieces[$y][$x];
    }

    /**
     * @param int $y
     * @param int $x
     * @return bool
     */
    public function hasPieceAtPosition($y, $x)
    {
        return $this->getPieceAtPosition($y, $x) !== '';
    }

}

/**
 * Gets input from the player and places pieces on the board.
 * Contains some minimal validation to prevent:
 * * Placing pieces on top of pieces
 * * Placing pieces outside of the board
 */
class Player
{
    const PIECE = 'O';

    public function placePiece(Board $board)
    {
        list($y, $x) = $this->getMovePosition($board);

        $board->addPiece($y, $x, self::PIECE);
    }

    private function getMovePosition(Board $board)
    {
        do {
            $position = [
                $this->getInput('Row'),
                $this->getInput('Col')
            ];

            $valid = !$board->hasPieceAtPosition($position[0],$position[1]);

            if (!$valid) {
                echo "Piece already at this position\n";
            }

        } while (!$valid);

        return $position;
    }

    private function getInput($prompt)
    {
        echo "$prompt: ";

        do {
            $input = trim(readline()); // todo get player typed into from console

            $valid = $input >= 0 && $input <= 2;

            if (!$valid) {
                echo "Input must be in range [0, 2]\n";
            }

        } while (!$valid);

        return $input;
    }
}

/**
 * Enemy player. Automatically places pieces on the board according to some rules.
 */
class AI
{
    const PIECE = 'X';
    const BADPIECE = 'O';
    /**
     * This function should call:
     *
     * $board->addPiece($y, $x, self::PIECE);
     *
     * use getMovePosition
     *
     * @param Board $board
     */
    public function placePiece(Board $board)
    {
//        todo...
        list($y, $x) = $this->getMovePosition($board);

        $board->addPiece($y, $x, self::PIECE);

    }

    /**
     * Return the position the AI wants to place a piece at
     *
     * This function should return [$y, $x] or null
     *
     * $y is the 0-index row
     * $x is the 0-index column
     *
     * The AI should use this logic in order:
     * 1. Place a piece that makes the AI win
     * 2. Or place a piece that prevents the player from winning
     * 3. Or place the piece in any position
     *
     * @param Board $board
     * @return int[]|null
     */
    private function getMovePosition(Board $board)
    {
        $position = null;

//        todo
        for($i=0;$i<Board::ROWS;$i++)
        {
                if(!($board->hasPieceAtPosition($i, 0)) && $board->getPieceAtPosition($i, 1)==self::PIECE && $board->getPieceAtPosition($i, 2)== self::PIECE)
                {
                        $position=[$i,0];
                }
                else if(!($board->hasPieceAtPosition($i, 1)) && $board->getPieceAtPosition($i, 0)==self::PIECE && $board->getPieceAtPosition($i, 2)== self::PIECE)
                {
                        $position=[$i,1];
                }
                else if(!($board->hasPieceAtPosition($i, 2)) && $board->getPieceAtPosition($i, 0)==self::PIECE && $board->getPieceAtPosition($i, 1)== self::PIECE)
                {
                        $position=[$i,2];
                }
        }
        for($j=0;$j<Board::COLUMNS;$j++)
        {
                if(!($board->hasPieceAtPosition(0, $j)) && $board->getPieceAtPosition(1, $j)==self::PIECE && $board->getPieceAtPosition(2, $j)== self::PIECE)
                {
                        $position=[0,$j];
                }
                else if(!($board->hasPieceAtPosition(1, $j)) && $board->getPieceAtPosition(0, $j)==self::PIECE && $board->getPieceAtPosition(2, $j)== self::PIECE)
                {
                        $position=[1,$j];
                }
                else if(!($board->hasPieceAtPosition(2, $j)) && $board->getPieceAtPosition(0, $j)==self::PIECE && $board->getPieceAtPosition(1, $j)== self::PIECE)
                {
                        $position=[2,$j];
                }
        }
        if($board->getPieceAtPosition(1, 1)==self::PIECE)
        {
                if(!($board->hasPieceAtPosition(0, 0)) && $board->getPieceAtPosition(2, 2)==self::PIECE)
                {
                        $position = [0,0];
                }
                else if(!($board->hasPieceAtPosition(2, 2)) && $board->getPieceAtPosition(0, 0)==self::PIECE)
                {
                        $position = [2,2];
                }
                else if(!($board->hasPieceAtPosition(0, 2)) && $board->getPieceAtPosition(2, 0)==self::PIECE)
                {
                        $position = [0,2];
                }
                else  if(!($board->hasPieceAtPosition(2, 0)) && $board->getPieceAtPosition(0, 2)==self::PIECE)
                {
                        $position = [2,0];
                }
        }

# 2. Or place a piece that prevents the player from winning. 
   if(is_null($position))
   {
        for($i=0;$i<Board::ROWS;$i++)
        {
                if(!($board->hasPieceAtPosition($i, 0)) && ($board->getPieceAtPosition($i, 1)==self::BADPIECE) && ($board->getPieceAtPosition($i, 2)== self::BADPIECE))
                {
                        $position=[$i,0];
                }
                else if(!($board->hasPieceAtPosition($i, 1)) && ($board->getPieceAtPosition($i, 0)==self::BADPIECE) && ($board->getPieceAtPosition($i, 2)== self::BADPIECE))
                {
                        $position=[$i,1];
                }
                else if(!($board->hasPieceAtPosition($i, 2)) && ($board->getPieceAtPosition($i, 0)==self::BADPIECE) && ($board->getPieceAtPosition($i, 1)== self::BADPIECE))
                {
                        $position=[$i,2];
                }
        }
        for($j=0;$j<Board::COLUMNS;$j++)
        {
                if(!($board->hasPieceAtPosition(0, $j)) && ($board->getPieceAtPosition(1, $j)==self::BADPIECE) && ($board->getPieceAtPosition(2, $j)== self::BADPIECE))
                {
                        $position=[0,$j];
                }
                else if(!($board->hasPieceAtPosition(1, $j)) && ($board->getPieceAtPosition(0, $j)==self::BADPIECE) && ($board->getPieceAtPosition(2, $j)== self::BADPIECE))
                {
                        $position=[1,$j];
                }
                else if(!($board->hasPieceAtPosition(2, $j)) && ($board->getPieceAtPosition(0, $j)==self::BADPIECE) && ($board->getPieceAtPosition(1, $j))== self::BADPIECE)
                {
                        $position=[2,$j];
                }
        }

        if($board->getPieceAtPosition(1, 1)==self::BADPIECE)
        {
                if(!($board->hasPieceAtPosition(0, 0)) && $board->getPieceAtPosition(2, 2)==self::BADPIECE)
                {
                        $position = [0,0];
                }
                else if(!($board->hasPieceAtPosition(2, 2)) && $board->getPieceAtPosition(0, 0)==self::BADPIECE)
                {
                        $position = [2,2];
                }
                else if(!($board->hasPieceAtPosition(0, 2)) && $board->getPieceAtPosition(2, 0)==self::BADPIECE)
                {
                        $position = [0,2];
                }
                else  if(!($board->hasPieceAtPosition(2, 0)) && $board->getPieceAtPosition(0, 2)==self::BADPIECE)
                {
                        $position = [2,0];
                }
        }
   }

  if(is_null($position))
  {
        $blankpositions=$board->getBlankPositions();
        $numberpositions = count($board->getBlankPositions());
        $chooseposition = rand(1,$numberpositions);
        $position = $blankpositions[$chooseposition - 1];
    }

        return $position;
    }
}


/**
 * @throws Exception If the AI does not go for the win
 */
function test_go_for_win()
{
    $board = new Board;
    $ai    = new AI;

    $board->addPiece(0, 0, AI::PIECE);
    $board->addPiece(0, 1, AI::PIECE);

    $ai->placePiece($board);

    if ($board->getPieceAtPosition(0, 2) !== AI::PIECE) {
        throw new \Exception('AI Should go for win');
    }
}

function debug_board($board)
{
    return str_replace("\n", "NEW LINE\n", $board);
}

/**
 * The board should be displayed in a grid.
 * A blank space: -
 * A piece: X or O
 *
 * Three columns in three rows
 *
 * Examples:
 * space: [row, column, X or O]
 * where data is a list of spaces
 *
 * data: [ [0, 0, O], [1, 0, O], [2, 0, O] ]
 * O--
 * O--
 * O--
 *
 * data: [ [1, 1, X] ]
 * ---
 * -X-
 * ---
 *
 * @throws Exception If board is rendered incorrectly.
 */
function test_display_board()
{
    $board = new Board;

    $board->addPiece(0, 0, 'O');
    $board->addPiece(1, 1, 'O');
    $board->addPiece(2, 2, 'O');

    $actual = (string)$board;

    $expected =  "O--\n-O-\n--O\n";  //Please note: I had to change this. It was not displaying the expect value after debug_board correctly.


    if ($actual !== $expected) {
        echo "Expected:\n", debug_board($expected);
        echo "\nActual:\n", debug_board($actual), "\n";
        throw new \Exception('test_display_board: Board should render correctly');
    }
}

/**
 * @throws Exception If the AI does not prevent the player from winning
 */
function test_prevent_loss()
{
    $board = new Board;
    $ai    = new AI;

    $board->addPiece(0, 2, Player::PIECE);
    $board->addPiece(1, 1, Player::PIECE);

    $ai->placePiece($board);

    if ($board->getPieceAtPosition(2, 0) !== AI::PIECE) {
        throw new \Exception('AI Should try to prevent player from winning');
    }
}

/**
 * @throws Exception When a winning piece combination does not trigger a win
 */
function test_player_wins()
{
    $board = new Board;

    $board->addPiece(0, 0, Player::PIECE);
    $board->addPiece(0, 1, Player::PIECE);
    $board->addPiece(0, 2, Player::PIECE);

    if ($board->getWinner() !== Player::PIECE) {
        throw new \Exception("Player should be able to win");
    }
}

function run_tests()
{
    test_display_board();
    test_player_wins();

    test_go_for_win();
    test_prevent_loss();
}

try {
    run_tests();
} catch (\Exception $e) {
    echo "Tests failed, game aborted\nMessage: ", $e->getMessage(), "\n";
    exit(1);
}

$board  = new Board;
$ai     = new AI;
$player = new Player;


/*
 * TODO Game Loop
 *
 * The game should be running if
 * 1. Nobody has won
 * 2. And the board is not full
 *
 * The AI goes first
 *
 * After the AI places a piece, display the board using the __toString method
 *
 * After someone places a piece, check if they won.
 *
 * If someone won display the message and exit
 *
 * If the board is full, display a message and exit
 */

$done = 0;

while($done == 0)
{
	if($board->isFull())
	{
		shell_exec('clear');
		echo "\nIt was a cat's game!\n";
		echo (string) $board;
		$done=1;
	}
	else
	{
		shell_exec('clear');
		$ai->placePiece($board);
		$won = $board->getWinner();
        	if($won == 'X')
		{
			shell_exec('clear');
			echo "\nSorry, You Lost!\n";
			echo (string) $board;
			$done =1;
	
		}
		else
		{
			echo (string) $board;
			$player->placePiece($board);
        		$won=$board-> getWinner();
			if($won=='O')
			{
				shell_exec('clear');
   				echo "\nYou won!!\n";
				echo (string) $board;
  	 			$done=1;
			}
		}
	}

}
