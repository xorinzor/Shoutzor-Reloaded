<?php

//Set the SiteURL and check if it needs to use HTTP or HTTPS
cl::g("tpl")->assign("SITEURL", 			SITEURL);
cl::g("tpl")->assign("STATIC_SITEURL", 		cl::g("tpl")->get_template_vars("SITEURL") . "static/");
cl::g("tpl")->assign("STATIC_TEMPLATEURL", 	cl::g("tpl")->get_template_vars("SITEURL") . "static/themes/" . cl::g("display")->getTheme()->getType() . "/" . cl::g("display")->getTheme()->getName() . "/");

cl::g("tpl")->assign("BASEURL",				cl::g("tpl")->get_template_vars("SITEURL") . cl::g("display")->getTheme()->getType() . "/");
cl::g("tpl")->assign("PAGEURL", 			cl::g("display")->getPage()->getUrl());

cl::g("tpl")->assign("PAGETITLE", 			cl::g("display")->getPage()->getTitle());		    //Set the title of the page
cl::g("tpl")->assign("SITETITLE", 		    cl::g("settings")->get("sitetitle")->getValue());	//Set the title of the website
cl::g("tpl")->assign("SUPPORTMAIL", 		cl::g("settings")->get("supportmail")->getValue());	//Set the support email address of the website
cl::g("tpl")->assign("COPYRIGHT", 			cl::g("settings")->get("copyright")->getValue()); 		//Set the footer text