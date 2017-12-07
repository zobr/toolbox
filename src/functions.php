<?php

namespace Zobr\Toolbox;

function fancy_dump(...$objects) {
    foreach ($objects as $object) {
        echo '<pre>';
        var_dump($object);
        echo '</pre>';
    }
}
