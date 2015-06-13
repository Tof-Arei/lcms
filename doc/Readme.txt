Light CMS by Arei

Version : 1.0b

Changelog :
1.0b [Arei]:
- Beta release


Thanks to/Credits :
- The FluxCP developers and contributors.
- The Hercules developers and contributors.
- Feefty for his invaluable help through his support addon.
- The CKEditor and HTMLPurifier developers.
- La Semeuse coffee brand and my expresso machine.
- My snakes and neighbors for putting up with me cursing PHP countless times
- And YOU for using this addon!


Description :
The purpose of Light CMS is to provide a simple CMS for Hercules FluxCP.
The main advantage of this addon over adding pages manually is that you don't
have to directly access the web server to add new contents. New pages are added
using a WYSIWYG editor rather than editing php/html code manually and upload modifications.
This plugin also allows the Hercules/FluxCP admins to add authors who will be able to add new
LCMS content using the addon's own permission system.

The purpose of Light CMS is NOT to be a powerful CMS the same way FluxCP's purpose
is not really to be your main website. Light CMS is a tool to provide a way to add and update
simple content to FluxCP, such as a download page, without having to do it manually.

Depending on the feedbacks, my own needs and laziness I might try to turn this addon into something
more powerful in the future.

Please also take note that I'm learning how to develop for FluxCP without documentation
while developing this plugin. There are errors, there are a lot of bad design choice and I will do my best
to fix errors and optimize my code with regular updates, but don't expect a perfect piece of software just yet.

Then again, I can't stress you enough to backup all your FluxCP file before installation and try the
addon out in a testing environment to see if everything works according to your needs and without errors
before uploading it to your production server.


Features :
- Light Content Management System with simple permissions
- WYSIWYG editor (CKEditor)
- HTML parser (HTMLPurifier)
- Author system with simple permissions
- Simple content moderation/validation system
- Fully translatable, comes with a free (untested) french translation


How to install :
1. Copy the lcms base directory into the FluxCP/addons directory

2. Execute FluxCP/addons/lcms/sql/install.sql into your Hercules/FluxCP sql database

   (Make a backup of the 3 FluxCP main files you are about to edit!)
3. Open FluxCP/index.php, goto line 58, add a new line and paste the following code :

// LCMS functions
require_once FLUX_ROOT . '/' . FLUX_ADDON_DIR . '/lcms/modules/lcms/functions.inc.php';

4. Open FluxCP/themes/default/header.php goto line 25, add a new line and paste the following code :

                <!-- CKEditor import for Light CMS -->
                <script type="text/javascript" src="<?php echo Flux::config('BaseURI').FLUX_ADDON_DIR."/lcms/lib/ckeditor/ckeditor.js" ?>"></script>

   Note: You must add this code for EVERY theme into header.php for this addon to work (untested) with them, the line might not be exactly the same too.
         Simply paste the code before <script type="text/javascript">

5. Open FluxCP/lib/Flux/Template.php, goto line 476, add a new line and paste the following code

                // Hook for LCMS dynamic menus
                if (!$adminMenus) {
                    $session = Flux::$sessionData;
                    $lcms = new Lcms_Functions($session);
                    $allowedItems = $lcms->getLcmsMenuItems($allowedItems, $this);
                }

6. Save the edited files and voilà!

(Optional)
7. Edit values in addon.php and access.php according to your preferences

8. Give HTMLPurifier writing permissions to enable the cache in addon.php.
   Enter the following command in a console with root permissions in FluxCP directory

chown -R www-data:www-data addons/lcms/lib/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer



How to uninstall :
1. Delete the FluxCP/addons/lcms directory

2. Execute FluxCP/addons/lcms/sql/uninstall.sql into your Hercules/FluxCP sql database


How to use :
1. Install the addon. That will add the Light CMS management menu.
   The menu is accessible to every logged user, but they have to be given author rights before they can post content.
   
   (Regarding security, authors who are not admins on the Hercules server can only access their own content and add new content.
   Be careful, even though the addon is protected against code injection, you should only give author permissions to people you trust
   because their content will appear on the website without confirmation.)

2. Give yourself author permissions using the "Add Authors" submenu.
3. Add content using the "Add [Content]" button.
    3.1 Adding a new module.
        Modules are used to group pages and add them dynamically to the FluxCP menu
        you need at least one if you want your content to appear on FluxCP.

        The name field will be displayed in the FluxCP menu as a category

    3.2 Adding a new page
        Pages are embedded inside modules and are used to display content.
        You must always link a page to an existing module.

        Select "Page" from the "Type" dropdown box when adding new content to
        add a new page.
        The access dropdown box is used to set the page viewing permissions. The valuee are
        the values used by FluxCP.

4. Edit/delete content
    4.1 Editing/deleting content (as an author)
        Users can edit and delete their own content using the corresponding buttons
        in the "My content" menu.

    4.2 Editing/deleting content (as an admin)
        Only admins (from a Hercules/FluxCP point of view) can see and edit all
        the LCMS content.
        They can do so by using the corresponding buttons in the "Manage content" menu.

5. Managing authors
    5.1 Adding new authors
        Only admins can add authors.
        They can do so by using the "Manage Content" menu.
       
    5.2 Editing/deleting authors
        Only admins can edit authors.
        They can do so by using the corresponding buttons in the "Manage content" menu.

6. Understanding the LCMS permission system
   The LCMS permission system is actually quite simple. When it comes to displaying content to users,
   the Hercules/FluxCP permissions are used, nothing more. When it comes to manage LCMS content, the
   addon will use it's own permission system: It works the same way as Hercules/FluxCP permission system, but uses it's own value
   within LCMS.

   For example, an user who is a simple user on Hercules/FluxCP (group_id=0) can be an administrator on LCMS side (access=99).
   A LCMS admin can access, moderate and edit any content belonging to users with a lower level of permission.
   i.e : A high-gm on LCMS (access=2) will be able to manage his own content and users below his level content (0=normal and 1=low-gm).
   Note: Only an administrator on Hercules/FluxCP side will be able to manage LCMS "admins" and their content.
   So basically, every author is also a moderator starting from low-gm (access=1).
