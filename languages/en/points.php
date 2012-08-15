<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: E ×12, A ×9, I ×9, O ×8, N ×6, R ×6, T ×6, L ×4, S ×4, U ×4
2 points: D ×4, G ×3
3 points: B ×2, C ×2, M ×2, P ×2
4 points: F ×2, H ×2, V ×2, W ×2, Y ×2
5 points: K ×1
8 points: J ×1, X ×1
10 points: Q ×1, Z ×1
*/

return array(
    'a' => array('points' => 1, 'quantity' => 9),
    'b' => array('points' => 3, 'quantity' => 2),
    'c' => array('points' => 3, 'quantity' => 2),
    'd' => array('points' => 2, 'quantity' => 4),
    'e' => array('points' => 1, 'quantity' => 12),
    'f' => array('points' => 4, 'quantity' => 2),
    'g' => array('points' => 2, 'quantity' => 3),
    'h' => array('points' => 4, 'quantity' => 2),
    'i' => array('points' => 1, 'quantity' => 9),
    'j' => array('points' => 8, 'quantity' => 1),
    'k' => array('points' => 5, 'quantity' => 1),
    'l' => array('points' => 1, 'quantity' => 4),
    'm' => array('points' => 3, 'quantity' => 2),
    'n' => array('points' => 1, 'quantity' => 6),
    'o' => array('points' => 1, 'quantity' => 8),
    'p' => array('points' => 3, 'quantity' => 2),
    'q' => array('points' => 10, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 6),
    's' => array('points' => 1, 'quantity' => 4),
    't' => array('points' => 1, 'quantity' => 6),
    'u' => array('points' => 1, 'quantity' => 4),
    'v' => array('points' => 4, 'quantity' => 2),
    'w' => array('points' => 4, 'quantity' => 2),
    'x' => array('points' => 8, 'quantity' => 1),
    'y' => array('points' => 4, 'quantity' => 2),
    'z' => array('points' => 10, 'quantity' => 1)
);
