# stageverslag-wordpress #
## What is this repository? ##

Wordpress-powered API for the stageverslag-react repository


## Tools & Technologies ##

**CMS:**
* WordPress

**Back-end tools**
* Composer (package manager)

~~**Front-end tools**~~
~~* Webpack (build tool for front-end packages, scripts and stylesheets)~~
~~* Sass (CSS Pre-processor)~~

## Wordpress ##
WordPress is a cms system we use to build easy to maintain sites. 
It's installed in the folder /webroot/wp/ by composer. 
The admin url is no longer `/wp-admin/` but `/wp/wp-admin/` since WordPress is installed in a subdirectory.

### Composer ###
Composer is the package manager used to manage **WordPress plugins**, **WordPress** itself and **third party libraries**.

### Installing Plugins ###
Plugins can be added by modifying the composer.json file. Simply add `"wpackagist-plugin/[PLUGIN NAME]": "*"` and 
run `composer update` in your terminal from the root of your project.

#### Custom Plugins ####
If you have custom plugins or have payed plugins from a vendor that is not supporting composer, follow the steps below:

1. Download your plugin;

2. Extract your plugin to the folder `webroot/assets/plugins/`;

3. Add your plugin to the `.gitignore` file under the comment `# Don't ignore our custom plugins` you can list plugins that are needed 
in git: `!/webroot/assets/plugins/[PLUGIN NAME]`.

4. Activate your plugin in the admin panel, and have fun!

### WP CLI ###
If you want to use the WP CLI tool feel free! For commands checkout http://wp-cli.org/

### Webpack ###
We use the Burst webpack build chain. If you go to the build folder on the terminal run `npm run watch` to watch you source files

### Source files ###
The source files are located in the src folder. Files are seperated between type, so all js files live in the js folder.

### Composer ###
Every time you pull your project you should run `composer install` to be sure you have the latest changes

### WP DB Sync ###
You can sync the local database to the development server with the WP Sync DB plugin. Go to the admin panel -> Settings -> Migrate DB.
You can connect to the online acceptance database by copying and pasting the key in the box and doing the sync. 

### Contribution guidelines ###
When you are planning to contribute to this repository, make sure to check your code and make sure that other users
don't experience problems during installations.
Never use this repository as your project repository. This should be a clean and up to date WordPress installation with basic theming.
This is a starting point for new WordPress templates that can be used as a reference.

### Who do I talk to? ###
For questions about Wordpress or this repository you can address questions to Floris Schippers
