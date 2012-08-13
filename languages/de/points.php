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
    'a' => 1,
    'ä' => 6,
    'b' => 3,
    'c' => 4,
    'd' => 1,
    'e' => 1,
    'f' => 4,
    'g' => 2,
    'h' => 2,
    'i' => 1,
    'j' => 6,
    'k' => 4,
    'l' => 2,
    'm' => 3,
    'n' => 1,
    'o' => 2,
    'ö' => 8,
    'p' => 4,
    'q' => 10,
    'r' => 1,
    's' => 1,
    't' => 1,
    'u' => 1,
    'ü' => 6,
    'v' => 1,
    'w' => 3,
    'x' => 8,
    'y' => 10,
    'z' => 3
);
