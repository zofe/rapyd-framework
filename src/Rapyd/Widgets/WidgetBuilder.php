<?php

namespace Rapyd\Widgets;

class WidgetBuilder
{

    public $widget;

    public function __construct($classname)
    {
        $this->widget = $classname;
    }

    public function createBuilder()
    {
        return new $this->widget;
    }

}
