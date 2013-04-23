<?php
class rpd_error_controller extends rpd_controller_controller {

    function code($code)
    {
        if ($code=='404')
        {
                $page = '404';
        }
        else
        {
                $page = 'error';
        }
        echo $this->view('errors/'.$page);
    }


}
?>
