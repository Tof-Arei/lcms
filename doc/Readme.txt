Light CMS V1.0b by Arei


Credits/Thanks to :
- The FluxCP developers and contributors.
- The Hercules developers and contributors.
- Feefty for his invaluable help through his support addon.
- The CKEditor and HTMLPurifier developers.
- La Semeuse coffee brand and my expresso machine.
- My snakes and neighbors for putting up with me cursing PHP/myself countless times
- And YOU for using this addon!


Description :
The purpose of Light CMS is to provide a simple CMS for Hercules FluxCP.
The main advantage of this addon over adding pages manually is that you don't have to directly access the web server to add new contents. New pages are added using a WYSIWYG editor rather than editing php/html code manually and upload modifications.
This plugin also allows the Hercules/FluxCP admins to add authors who will be able to add new LCMS content using the addon's own permission system.

The purpose of Light CMS is NOT to be a powerful CMS the same way FluxCP's purpose is not really to be your main website. Light CMS is a tool to provide a way to add and update simple content to FluxCP, such as a download page, without having to do it manually.

Depending on the feedbacks, my own needs and laziness I might try to turn this addon into something more powerful in the future.

Please also take note that I'm learning how to develop for FluxCP without documentation while developing this plugin. There are errors, there are a lot of bad design choice and I will do my best to fix errors and optimize my code with regular updates, but don't expect a perfect piece of software just yet.

Then again, I can't stress you enough to backup all your FluxCP file before installation and try the addon out in a testing environment to see if everything works according to your needs and without errors before uploading it to your production server.


Features :
- Light Content Management System with simple permissions
- WYSIWYG editor (CKEditor)
- HTML parser (HTMLPurifier)
- Author system with simple permissions
- Simple content moderation/validation system
- No manual files edit needed besides addon installation and manual page hooking
- (Almost) fully translatable, comes with a free (untested) french translation


How to install :
1. Copy the "lcms" base directory from the archive into the FluxCP/addons directory

2. Execute FluxCP/addons/lcms/sql/install.sql into your Hercules/FluxCP sql database

(Make a backup of the 2 FluxCP main files you are about to edit!)
3. Open FluxCP/index.php, goto line 58, add a new line and paste the following code :

// LCMS functions
require_once FLUX_ROOT . '/' . FLUX_ADDON_DIR . '/lcms/modules/lcms/functions.inc.php';

4. Open FluxCP/lib/Flux/Template.php, goto line 476, add a new line and paste the following code

// Hook for LCMS dynamic menus
if (!$adminMenus) {
    $session = Flux::$sessionData;
    $lcms = new Lcms_Functions($session);
    $allowedItems = $lcms->getLcmsMenuItems($allowedItems, $this);
}

5. Save the edited files and voilà!

(Optional)
6. Edit values in addon.php and access.php according to your preferences

7. Give HTMLPurifier writing permissions to enable the cache in addon.php.
Enter the following command in a console with root permissions in FluxCP directory

chown -R www-data:www-data addons/lcms/lib/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer



How to uninstall :
1. Delete the FluxCP/addons/lcms directory

2. Execute FluxCP/addons/lcms/sql/uninstall.sql into your Hercules/FluxCP sql database

3. Restore FluxCP files modified by installation steps 3 and 4

How to use :
1. Install the addon. That will add the Light CMS management menu.
The menu is accessible to every logged user, but they have to be given author rights before they can post content.
   
(Regarding security: Be careful, even though the addon is protected against code injection, you should only give author permissions to people you trust because their content will appear on the website without confirmation.)

2. Give yourself author permissions using the "Add Authors" submenu.
3. Add content using the "Add" button in the according menu.
    3.1 Adding a new module.
    Modules are used to group pages and insert them dynamically into the FluxCP menu you need at least one module if you want to be able to create pages.
    Note: An author can't create any page without an existing and accessible module.

    The access dropdown box is used to the the module using permissions for authors, it uses LCMS permissions.
    The name field will be displayed in the FluxCP menu as a category

    3.2 Adding a new page
    Pages are embedded inside modules and are used to display content.
    You must always link a page to an existing module.

    The access dropdown box is used to set the page viewing permissions, it uses Hercules/FluxCP permissions
        
    3.2.1 Hooking a LCMS page to a FluxCP page manually (Backing up the original files is strongly recommended)
    Open the desired page from FluxCP/themes/<your theme>/ directory and replace the content with the following code :

    <?php
    if (!defined('FLUX_ROOT')) exit;

    $page_id = 1; // id of the existing page to hook
    $lcms = new Lcms_Functions($session);
    echo $lcms->hookPage($page_id);
    ?>

4. Edit/delete content
Use the according buttons in the according menus.

5. Managing authors
Authors can be managed by using the "Manage authors" menu.


6. Understanding the LCMS permission system
The LCMS permission system is actually quite simple. When it comes to displaying content to users, the Hercules/FluxCP permissions are used, nothing more. When it comes to manage LCMS content, the addon will use it's own permission system: It works the same way as Hercules/FluxCP permission system, but uses it's own value within LCMS.

For example, an user who is a simple user on Hercules/FluxCP (group_id=0) can be an administrator on LCMS side (access=99).
A LCMS admin can access, moderate and edit any content belonging to users with a lower level of permission.
i.e : A high-gm on LCMS (access=2) will be able to manage his own content and users below his level content (0=normal and 1=low-gm).