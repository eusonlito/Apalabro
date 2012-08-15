<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: E ×15, N ×9, S ×7, I ×6, R ×6, T ×6, U ×6, A ×5, D ×4
2 points: H ×4, G ×3, L ×3, O ×3
3 points: M ×4, B ×2, W ×1, Z ×1
4 points: C ×2, F ×2, K ×2, P ×1
6 points: Ä ×1, J ×1, Ü ×1, V ×1
8 points: Ö ×1, X ×1
10 points: Q ×1, Y ×1
*/

return array(
    'a' => array('points' => 1, 'quantity' => 5),
    'ä' => array('points' => 6, 'quantity' => 1),
    'b' => array('points' => 3, 'quantity' => 2),
    'c' => array('points' => 4, 'quantity' => 2),
    'd' => array('points' => 1, 'quantity' => 4),
    'e' => array('points' => 1, 'quantity' => 15),
    'f' => array('points' => 4, 'quantity' => 2),
    'g' => array('points' => 2, 'quantity' => 3),
    'h' => array('points' => 2, 'quantity' => 4),
    'i' => array('points' => 1, 'quantity' => 6),
    'j' => array('points' => 6, 'quantity' => 1),
    'k' => array('points' => 4, 'quantity' => 2),
    'l' => array('points' => 2, 'quantity' => 3),
    'm' => array('points' => 3, 'quantity' => 4),
    'n' => array('points' => 1, 'quantity' => 9),
    'o' => array('points' => 2, 'quantity' => 3),
    'ö' => array('points' => 8, 'quantity' => 1),
    'p' => array('points' => 4, 'quantity' => 1),
    'q' => array('points' => 10, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 6),
    's' => array('points' => 1, 'quantity' => 7),
    't' => array('points' => 1, 'quantity' => 6),
    'u' => array('points' => 1, 'quantity' => 6),
    'ü' => array('points' => 6, 'quantity' => 1),
    'v' => array('points' => 6, 'quantity' => 1),
    'w' => array('points' => 3, 'quantity' => 1),
    'x' => array('points' => 8, 'quantity' => 1),
    'y' => array('points' => 10, 'quantity' => 1),
    'z' => array('points' => 3, 'quantity' => 1)
);
