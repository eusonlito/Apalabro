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
    'a' => 1,
    'b' => 3,
    'c' => 3,
    'd' => 2,
    'e' => 1,
    'f' => 4,
    'g' => 2,
    'h' => 4,
    'i' => 1,
    'j' => 8,
    'k' => 10,
    'l' => 1,
    'm' => 2,
    'n' => 1,
    'o' => 1,
    'p' => 3,
    'q' => 8,
    'r' => 1,
    's' => 1,
    't' => 1,
    'u' => 1,
    'v' => 4,
    'w' => 10,
    'x' => 10,
    'y' => 10,
    'z' => 10
);
