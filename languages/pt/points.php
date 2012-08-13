<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: A ×14, E ×11, I ×10, O ×10, S ×8, U ×7, M ×6, R ×6, T ×5,
2 points: D ×5, L ×5, C ×4, P ×4
3 points: N ×4, B ×3, Ç ×2
4 points: F ×2, G ×2, H ×2, V ×2
5 points: J ×2
6 points: Q ×1
8 points: X ×1, Z ×1

While Ç is a separate tile, other diacritical marks are ignored. K, W, and Y are absent, since they are only present in loanwords in Portuguese.
*/

return array(
    'a' => 1,
    'b' => 3,
    'c' => 2,
    'ç' => 3,
    'd' => 2,
    'e' => 1,
    'f' => 4,
    'g' => 4,
    'h' => 4,
    'i' => 1,
    'j' => 5,
    'l' => 2,
    'm' => 1,
    'n' => 3,
    'o' => 1,
    'p' => 2,
    'q' => 6,
    'r' => 1,
    's' => 1,
    't' => 1,
    'u' => 1,
    'v' => 4,
    'x' => 8,
    'z' => 8
);
