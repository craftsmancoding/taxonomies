<?php
/**
 * A place for any global functions used in the unit tests.
 */

/**
 * Normalize (HTML) strings for valid comparison.
 */
function normalize_string($str) {
    $str = preg_replace('/\s+/',' ',$str);
    return trim($str);
}


/*EOF*/