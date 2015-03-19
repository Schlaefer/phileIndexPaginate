<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

class Paginator {

    public static function build($items, $itemsPerPage = 5) {
        $current = 0;

        $input = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if ($input) {
            $input--; // norm to base 0 index
            $first = $itemsPerPage * $input;
            if (count($items) - 1 >= $first) {
                $current = $input;
            } else {
                return false;
            }
        }

        $chunks = array_chunk($items, $itemsPerPage);

        $isFirst = $current === 0;
        $isLast = $current === count($chunks) - 1;
        $items = $chunks[$current];

        $current++; // externally we use base 1 index

        return compact('isFirst', 'isLast', 'current', 'items');
    }

}
