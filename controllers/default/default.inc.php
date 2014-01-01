<?php

if(cl::g("session")->isLoggedin()) header("location: ".SITEURL."dashboard/");