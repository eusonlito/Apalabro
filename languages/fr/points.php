<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: E ×15, A ×9, I ×8, N ×6, O ×6, R ×6, S ×6, T ×6, U ×6, L ×5
2 points: D ×3, G ×2, M ×3
3 points: B ×2, C ×2, P ×2
4 points: F ×2, H ×2, V ×2
8 points: J ×1, Q ×1
10 points: K ×1, W ×1, X ×1, Y ×1, Z ×1

Diacritical marks are ignored.
*/

return array(
    'a' => array('points' => 1, 'quantity' => 9),
    'b' => array('points' => 3, 'quantity' => 2),
    'c' => array('points' => 3, 'quantity' => 2),
    'd' => array('points' => 2, 'quantity' => 3),
    'e' => array('points' => 1, 'quantity' => 15),
    'f' => array('points' => 4, 'quantity' => 2),
    'g' => array('points' => 2, 'quantity' => 2),
    'h' => array('points' => 4, 'quantity' => 2),
    'i' => array('points' => 1, 'quantity' => 8),
    'j' => array('points' => 8, 'quantity' => 1),
    'k' => array('points' => 10, 'quantity' => 1),
    'l' => array('points' => 1, 'quantity' => 5),
    'm' => array('points' => 2, 'quantity' => 3),
    'n' => array('points' => 1, 'quantity' => 6),
    'o' => array('points' => 1, 'quantity' => 6),
    'p' => array('points' => 3, 'quantity' => 2),
    'q' => array('points' => 8, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 6),
    's' => array('points' => 1, 'quantity' => 6),
    't' => array('points' => 1, 'quantity' => 6),
    'u' => array('points' => 1, 'quantity' => 6),
    'v' => array('points' => 4, 'quantity' => 2),
    'w' => array('points' => 10, 'quantity' => 1),
    'x' => array('points' => 10, 'quantity' => 1),
    'y' => array('points' => 10, 'quantity' => 1),
    'z' => array('points' => 10, 'quantity' => 1)
);
