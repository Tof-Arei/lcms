<?php if (!defined('FLUX_ROOT')) exit;
return array(
        // LCMS configurable options
        'LcmsPagesPerPage'            => 10,                    // Max displayed pages per page
        'LcmsModulesPerPage'          => 10,                    // Max displayed modules per page
        'LcmsAuthorsPerPage'          => 10,                    // Max displayed authors per page
        'LcmsCreatePageMinLevel'      => AccountLevel::NORMAL,  // Minimum LCMS AccountLevel to create pages
        'LcmsCreateModuleMinLevel'    => AccountLevel::HIGHGM,  // Minimum LCMS AccountLevel to create modules
        'LcmsCreateAuthorMinLevel'    => AccountLevel::ADMIN,   // Minimum LCMS AccountLevel to create authors
        'LcmsValidationEnable'        => true,                  // If true, any content posted by users below BypassValidationLevel level will require validation from an higher level user
        'LcmsValidationBypassLevel'   => AccountLevel::LOWGM,   // If EnableContentValidation=true, defines the access level needed to bypass validation
        // Important note : LCMS permissions (access) use the same permission pattern as Hercules/FluxCP (group_id)
        //                  An author with an admin level on LCMS will NOT be granted admin level on Hercules/FluxCP and vice-versa.
        //                  Please also note that the only users able to delete LCMS "admin" (access=99) content are Hercules/FluxCP admins (group_id=99)
        //                  I'll work on making the permission system simpler in the upcoming updates.
    
        // LCMS libraries options
        'LcmsEnableHTMLPurifierCache' => false,                 // If true, activates HTMLPurifier cache to improve performances
                                                                // Warning: You have to give HTMLPurifier permissions to write on your server
                                                                // see the optional installation step 8 in Readme.txt for more informations

    
        // LCMS dev configuration (If you don't know what you are doing, don't do it.)
	'MenuItems'		=> array(
		'Other'	=> array(
			'Light CMS' => array(
				'module' => 'lcms'
			)
		)
	),

	'SubMenuItems'	=> array(
		'lcms'	=> array(
			'index'          => 'My pages',
                        'page'           => 'Manage pages',
                        'module'         => 'Manage modules',
                        'author'         => 'Manage authors',
		)
	),

	'FluxTables'	=> array(
                'lcms_author'   => 'cp_lcms_author',
		'lcms_module' 	=> 'cp_lcms_module',
                'lcms_page'     => 'cp_lcms_page'
	),
    
        'LcmsValues'         =>  array(
                'author' => array(
                        'account_id'      => array('int', '11', 'not null', ''),
                        'access'          => array('int', '2', 'not null', '0')
                ),
                'module' => array(
                        'id'              => array('int', '11', 'not null', '0'),
                        'account_id'      => array('int', '11', 'not null', 'author()'),
                        'access'          => array('int', '2', 'not null', '0'),
                        'name'            => array('string', '255', 'not null')
                ),
                'page'   => array(
                        'id'              => array('int', '11', 'not null', '0'),
                        'module_id'       => array('int', '11',  'not null', ''),
                        'account_id'      => array('int', '11',  'not null', 'author()'),
                        'status'          => array('int', '2',  'not null', '-1'),
                        'access'          => array('int', '2',  'not null', '0'),
                        'date'            => array('string', 'DateTime', 'not null', 'now()'),
                        'name'            => array('string', '255', 'not null'),
                        'content'         => array('string', '50000', 'not null')
                )
        )
)
?>