<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 19.11.2015
 * Time: 17:51
 */

/**
 * @var boolean $haveSeen
 * @var string $style
 * @var string $class
 */

if (is_null($haveSeen)) {
    echo '<span data-toggle="tooltip" title="Nicht angesehen" style="' . $style . '" class="' . $class . '"><i class="glyphicon glyphicon-eye-open"></i></span>';
}
