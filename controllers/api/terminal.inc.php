<?php

    ob_start();
        print_r($_SESSION);
        cl::g("tpl")->assign("SESSIONDATA", ob_get_contents());
    ob_end_clean();