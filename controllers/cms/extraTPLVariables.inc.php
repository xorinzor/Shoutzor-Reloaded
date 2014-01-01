<?php

cl::g("tpl")->assign("MENU", 				cl::g("menu")->getMenu());
cl::g("tpl")->assign("LOGGEDIN", 			cl::g("session")->isLoggedin());

//Add the metatags to the headEnd region
cl::g("regions")->getRegion("headEnd")->addContent(cl::g("webpage")->getMetaTags());

//Set the user permissions to adjust the template to
cl::g("tpl")->assign("PERMISSIONS", 		cl::g("permissions")->getPermissions());

//Assign the template regions
cl::g("tpl")->assign("TEMPLATEREGION", 		cl::g("regions")->getRegionsAndContent());

if(cl::g("session")->isLoggedin()):
	cl::g("tpl")->assign("USERNAME", 		cl::g("session")->getUser()->getEmail());
	cl::g("tpl")->assign("NAME", 			cl::g("session")->getUser()->getFullName());
endif;