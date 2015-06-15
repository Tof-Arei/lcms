<?php
return array(
        // Hercules/FluxCP permissions for LCMS pages
	'modules' => array(
		'lcms' => array(
                        // LCMS personal content management pages
			'index'         => AccountLevel::NORMAL,  // Minimum AccountLevel required to see the "My pages" menu
                        'edit'          => AccountLevel::NORMAL,  // Minimum AccountLevel required to access add/update/delete menus
                        'page.form'     => AccountLevel::NORMAL,  // Minimum AccountLevel required to access the page form
                        'module.form'   => AccountLevel::NORMAL,  // Mimimum AccountLevel required to access the module form
                        'author.form'   => AccountLevel::NORMAL,  // Minimum AccountLevel required to access the author form
                        // LCMS content moderation page
                        'page'          => AccountLevel::LOWGM,   // Minimum AccountLevel required to see the "Manage pages" menu
                        'module'        => AccountLevel::ADMIN,   // Minimum AccountLevel required to see the "Manage modules" menu
                        'author'        => AccountLevel::ADMIN,   // Minimum AccountLevel required to see the "Manage authors" menu
		)
	)
)
?>