<?php
defined('BASE_PATH') or die();

/*
http://en.wikipedia.org/wiki/Scrabble_letter_distributions

1 point: E ×13, A ×12, I ×8, R ×8, S ×8, N ×6, O ×5, T ×5, L ×4, U ×4
2 points: C ×3, D ×3, M ×3
3 points: B ×2, G ×2, P ×2
4 points: F ×1, V ×1
8 points: H ×1, J ×1, Q ×1, Z ×1
10 points: Ç ×1, L·L ×1, NY ×1, X ×1

Accents and diaereses are ignored; for example, À is played as A.
Nevertheless, there are special tiles for the C with cedilla Ç (ce trencada), the sign L·L representing the geminated ell (ela geminada),
as well as the digraph NY. Playing an N tile followed by a blank tile to form the digraph NY is not allowed.
Official rules treat the Q tile as just one letter, but usually Catalan players use the Q tile like the QU digraph and
all Catalan Scrabble Clubs use this de facto rule.
*/

return array(
    'a' => array('points' => 1, 'quantity' => 12),
    'b' => array('points' => 3, 'quantity' => 2),
    'c' => array('points' => 2, 'quantity' => 3),
    'ç' => array('points' => 10, 'quantity' => 1),
    'd' => array('points' => 2, 'quantity' => 3),
    'e' => array('points' => 1, 'quantity' => 13),
    'f' => array('points' => 4, 'quantity' => 1),
    'g' => array('points' => 3, 'quantity' => 2),
    'h' => array('points' => 8, 'quantity' => 1),
    'i' => array('points' => 1, 'quantity' => 8),
    'j' => array('points' => 8, 'quantity' => 1),
    'l' => array('points' => 1, 'quantity' => 4),
    'l·l' => array('points' => 10, 'quantity' => 1),
    'm' => array('points' => 2, 'quantity' => 3),
    'n' => array('points' => 1, 'quantity' => 6),
    'ny' => array('points' => 10, 'quantity' => 1),
    'o' => array('points' => 1, 'quantity' => 5),
    'p' => array('points' => 3, 'quantity' => 2),
    'q' => array('points' => 8, 'quantity' => 1),
    'r' => array('points' => 1, 'quantity' => 8),
    's' => array('points' => 1, 'quantity' => 8),
    't' => array('points' => 1, 'quantity' => 5),
    'u' => array('points' => 1, 'quantity' => 4),
    'v' => array('points' => 4, 'quantity' => 1),
    'x' => array('points' => 10, 'quantity' => 1),
    'z' => array('points' => 8, 'quantity' => 1)
);
