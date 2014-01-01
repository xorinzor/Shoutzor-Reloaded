<?php

    cl::g("tpl")->assign("CURRENTSESSION", session_id());
    cl::g("tpl")->assign("USERSESSIONS", cl::g("session")->getUserSessions());