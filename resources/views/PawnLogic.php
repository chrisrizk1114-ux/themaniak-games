<?php

namespace App\Services;

class PawnLogic
{
    /**
     * Validate if a pawn move is legal.
     * 
     * @param array $from [row, col]
     * @param array $to [row, col]
     * @param string $color 'white'|'black'
     * @param bool $isTargetEmpty
     * @param bool $isEnPassant
     * @return bool
     */
    public function isValidMove(array $from, array $to, string $color, bool $isTargetEmpty, bool $isEnPassant = false): bool
    {
        $direction = ($color === 'white') ? -1 : 1; // White moves up (negative row index), Black moves down
        $startRow = ($color === 'white') ? 6 : 1;   // Starting rows for 0-indexed 8x8 board
        
        $rowDiff = $to[0] - $from[0];
        $colDiff = $to[1] - $from[1];
        $absColDiff = abs($colDiff);

        // 1. Standard Forward Move
        if ($isTargetEmpty && $colDiff === 0 && $rowDiff === $direction) {
            return true;
        }

        // 2. Initial Double Move
        // Must be on start row, target must be empty, and path must be clear (handled by controller usually)
        if ($isTargetEmpty && $colDiff === 0 && $from[0] === $startRow && $rowDiff === (2 * $direction)) {
            return true;
        }

        // 3. Standard Capture
        // Must move diagonally forward by one square and target must not be empty
        if (!$isTargetEmpty && $absColDiff === 1 && $rowDiff === $direction) {
            return true;
        }

        // 4. En Passant
        if ($isEnPassant && $absColDiff === 1 && $rowDiff === $direction) {
            return true;
        }

        return false;
    }

    /**
     * Check if a pawn has reached the promotion rank.
     */
    public function canPromote(int $currentRow, string $color): bool
    {
        return ($color === 'white' && $currentRow === 0) || 
               ($color === 'black' && $currentRow === 7);
    }
}