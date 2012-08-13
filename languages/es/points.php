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
    'l' => 1,
    'm' => 3,
    'n' => 1,
    'ñ' => 8,
    'o' => 1,
    'p' => 3,
    'q' => 5,
    'r' => 1,
    's' => 1,
    't' => 1,
    'u' => 1,
    'v' => 4,
    'x' => 8,
    'y' => 4,
    'z' => 10
);
