<?php
class Controller_Main extends Controller
{
    function action_index()
    {
        $this->view->render('main_view.php', 'default_layout.php');
    }
}
