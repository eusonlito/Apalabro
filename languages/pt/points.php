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
    'a' => array('points' => 1, 'quantity' => 14),
    'b' => array('points' => 3, 'quantity' => 3),
    'c' => array('points' => 2, 'quantity' => 4),
    'ç' => array('points' => 3, 'quantity' => 2),
    'd' => array('points' => 2, 'quantity' => 5),
    'e' => array('points' => 1, 'quantity' => 11),
    'f' => array('points' => 4, 'quantity' => 2),
    'g' => array('points' => 4, 'quantity' => 2),
    'h' => array('points' => 4, 'quantity' => 2),
    'i' => array('points' => 1, 'quantity' => 10),
    'j' => array('points' => 5, 'quantity' => 2),
    'l' => array('points' => 2, 'quantity' => 5),
    'm' => array('points' => 1, 'quantity' => 6),
    'n' => array('points' => 3, 'quantity' => 4),
    'o' => array('points' => 1, 'quantity' => 10),
    'p' => array('points' => 2, 'quantity' => 4),
    'q' => array('points' => 6, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 6),
    's' => array('points' => 1, 'quantity' => 8),
    't' => array('points' => 1, 'quantity' => 5),
    'u' => array('points' => 1, 'quantity' => 7),
    'v' => array('points' => 4, 'quantity' => 2),
    'x' => array('points' => 8, 'quantity' => 1),
    'z' => array('points' => 8, 'quantity' => 1)
);
