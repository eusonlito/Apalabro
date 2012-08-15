<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: O ×15, A ×14, I ×12, E ×11
2 points: C ×6, R ×6, S ×6, T ×6
3 points: L ×5, M ×5, N ×5, U ×5
5 points: B ×3, D ×3, F ×3, P ×3, V ×3
8 points: G ×2, H ×2, Z ×2
10 points: Q ×1

The letters J, K, W, X, and Y are absent since these letters are used only in loanwords.
*/

return array(
    'a' => array('points' => 1, 'quantity' => 14),
    'b' => array('points' => 5, 'quantity' => 3),
    'c' => array('points' => 2, 'quantity' => 6),
    'd' => array('points' => 5, 'quantity' => 3),
    'e' => array('points' => 1, 'quantity' => 11),
    'f' => array('points' => 5, 'quantity' => 3),
    'g' => array('points' => 8, 'quantity' => 2),
    'h' => array('points' => 8, 'quantity' => 2),
    'i' => array('points' => 1, 'quantity' => 12),
    'l' => array('points' => 3, 'quantity' => 5),
    'm' => array('points' => 3, 'quantity' => 5),
    'n' => array('points' => 3, 'quantity' => 5),
    'o' => array('points' => 1, 'quantity' => 15),
    'p' => array('points' => 5, 'quantity' => 3),
    'q' => array('points' => 10, 'quantity' => 1),
    'r' => array('points' => 2, 'quantity' => 6),
    's' => array('points' => 2, 'quantity' => 6),
    't' => array('points' => 2, 'quantity' => 6),
    'u' => array('points' => 3, 'quantity' => 5),
    'v' => array('points' => 5, 'quantity' => 3),
    'z' => array('points' => 8, 'quantity' => 2)
);
