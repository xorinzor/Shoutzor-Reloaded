<?php

/* Prevent users from accessing this area without permissions */
if(!cl::g("session")->isLoggedin()) header("Location: ".SITEURL);