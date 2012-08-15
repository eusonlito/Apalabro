<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: A ×12, E ×12, O ×9, I ×6, S ×6, N ×5, L ×4, R ×5, U ×5, T ×4
2 points: D ×5, G ×2
3 points: C ×4, B ×2, M ×2, P ×2
4 points: H ×2, F ×1, V ×1, Y ×1
5 points: CH ×1, Q ×1
8 points: J ×1, LL ×1, Ñ ×1, RR ×1, X ×1
10 points: Z ×1

Stress accents are disregarded.
The letters K and W are absent since these two letters are rarely used in Spanish words.
According to FISE (Federación Internacional de Scrabble en Español) rules, a blank cannot be used to represent K or W.
*/

return array(
    'a' => array('points' => 1, 'quantity' => 12),
    'b' => array('points' => 3, 'quantity' => 2),
    'c' => array('points' => 3, 'quantity' => 4),
    'd' => array('points' => 2, 'quantity' => 5),
    'e' => array('points' => 1, 'quantity' => 12),
    'f' => array('points' => 4, 'quantity' => 1),
    'g' => array('points' => 2, 'quantity' => 2),
    'h' => array('points' => 4, 'quantity' => 2),
    'i' => array('points' => 1, 'quantity' => 6),
    'j' => array('points' => 8, 'quantity' => 1),
    'l' => array('points' => 1, 'quantity' => 4),
    'm' => array('points' => 3, 'quantity' => 2),
    'n' => array('points' => 1, 'quantity' => 5),
    'ñ' => array('points' => 8, 'quantity' => 1),
    'o' => array('points' => 1, 'quantity' => 9),
    'p' => array('points' => 3, 'quantity' => 2),
    'q' => array('points' => 5, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 5),
    's' => array('points' => 1, 'quantity' => 6),
    't' => array('points' => 1, 'quantity' => 4),
    'u' => array('points' => 1, 'quantity' => 5),
    'v' => array('points' => 4, 'quantity' => 1),
    'x' => array('points' => 8, 'quantity' => 1),
    'y' => array('points' => 4, 'quantity' => 1),
    'z' => array('points' => 10, 'quantity' => 1)
);
