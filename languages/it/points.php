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
    'a' => 1,
    'b' => 5,
    'c' => 2,
    'd' => 5,
    'e' => 1,
    'f' => 5,
    'g' => 8,
    'h' => 8,
    'i' => 1,
    'l' => 3,
    'm' => 3,
    'n' => 3,
    'o' => 1,
    'p' => 5,
    'q' => 10,
    'r' => 2,
    's' => 2,
    't' => 2,
    'u' => 3,
    'v' => 5,
    'z' => 8
);
