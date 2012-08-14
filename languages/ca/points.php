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
    'a' => 1,
    'b' => 3,
    'c' => 2,
    'ç' => 10,
    'd' => 2,
    'e' => 1,
    'f' => 4,
    'g' => 3,
    'h' => 8,
    'i' => 1,
    'j' => 8,
    'l' => 1,
    'l·l' => 10,
    'm' => 2,
    'n' => 1,
    'ny' => 10,
    'o' => 1,
    'p' => 3,
    'q' => 8,
    'r' => 1,
    's' => 1,
    't' => 1,
    'u' => 1,
    'v' => 4,
    'x' => 10,
    'z' => 8
);
