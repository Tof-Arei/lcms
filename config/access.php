<?php
return array(
        // Hercules/FluxCP permissions for LCMS pages
	'modules' => array(
		'lcms' => array(
                        // LCMS permission configuration
                                                                 // Note: you can set custom permissions on each page created with LCMS
			'index'         => AccountLevel::NORMAL, // Minimum AccountLevel required to access the "Light CMS" menu and "My Pages" page
                                                                 // Note: A module should be accessible to the user for him to be able to add pages
                        'page'          => AccountLevel::LOWGM,  // Minimum AccountLevel required to access the "Manage pages" menu
                                                                 // Note: A module should be accessible to the user for him to be able to add pages
                        'module'        => AccountLevel::ADMIN,  // Minimum AccountLevel required to access the "Manage modules" menu
                        'author'        => AccountLevel::ADMIN,  // Minimum AccountLevel required to access the "Manage authors" menu
                    
                        // LCMS interface pages access (don't edit these values unless you know what you are doing)
                        // Note: None user can access content above their author access level (if they have any) even if they can access the forms
                        //       Should somebody "hack" his way into the forms, no data will be displayed/added/edited/deleted without the proper access level
                        'edit'          => AccountLevel::NORMAL, // Minimum AccountLevel required to access add/update/delete menus
                        'page.form'     => AccountLevel::NORMAL, // Minimum AccountLevel required to access the page form
                        'module.form'   => AccountLevel::NORMAL, // Mimimum AccountLevel required to access the module form
                        'author.form'   => AccountLevel::NORMAL, // Minimum AccountLevel required to access the author form
		)
	)
)
?>