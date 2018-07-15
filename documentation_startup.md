# AceCIUser - User Administration System

## Setting Up the AceCIUser System

1. If you are using [Scotch Box](https://box.scotch.io) you'll need to upgrade your version of MySQL. 
   * From the Windows command prompt within your Scotch Box folder:
    
          vagrant ssh
          sudo apt-get update
          sudo apt-get update
          sudo apt-get upgrade
          sudo apt-get install mysql-server-5.6
          exit

2. Clone the ACECIUser project into your HTTP server's root folder:
   * If you are using ScotchBox you will want to:
   
     * Delete the public folder within your Scotch Box folder.
     * Use git or GitKraken to clone https://github.com/bitprojectspace/AceCIUser.git in the ScotchBox folder.
     * Rename the newly cloned `AceCIUser` folder to `public`
     * Remove the hidden `.git` folder in the `public` folder.

3. Pull in the required Composer dependencies:
   * Install [Composer](https://getcomposer.org/) to Windows. (Composer is already installed on your Scotch Box VM if you're comfortable using the Linux command prompt via `vagrant ssh`.)
   * At a command prompt (Windows or Linux) from the project root folder (the "public" folder when using Scotch Box) run:
   
         composer require mailgun/mailgun-php php-http/guzzle6-adapter php-http/message

4. Set the base CodeIgniter URL: (Every CodeIgniter project needs this to be set.)
   * Edit the `config/config.php` file:
     * Where #.#.#.# is your development server's IP address or domain:
     
           $config['base_url'] = 'http://#.#.#.#/';
			
5. Setup the database and required tables:
   * Edit the `config/database.php` to match your database server, for example:
    
         'hostname' => 'localhost',
         'username' => 'root',
         'password' => 'root',
         'database' => 'aceciuser'

   * Ensure that you have a database in your database server with the same name as the one used in your config/database.php file:
     * From your database SQL prompt (for example):
     
           CREATE database aceciuser;
			
   * In your browser open the page `/aceciuser_setting.php` page:
     * Hit the button, generate the tables.
     * Once you have verified that the required tables were create you should remove this `aceciuser_setting.php` script. (It can be retrieved from the project repo if you need it again.)

5. Test the login:
   * Open the `index.php/users/view_login.php` page. The default admin credentials are:
   
         username: admin@rrc.none
         password: rrc12345

6. Update the application routes: (optional)
   * You can set the login page as the first page of your app. For example, edit your `config/routes.php` file:
   
         $route['default_controller'] = 'users/view_login';	

7. Set the auto email service: (This doesn't need to be configured right away, but is required if you wish to allow users to reset their passwords, or to allow admins to email new users with their login credentials.)
   * We are using Mailgun service:
   
     * Talk to your client about getting a mailgun.com account.
     * Set your mail service key in the `Helpers/User_email_helper.php` file.

## Customizing User Permissions

User permissions are set within the `helpers/User_variables_helper.php` file.

The first function in this file `variables_get_auth()` is a list of user permissions. Anyone without permission will be kicked out.

Before setting your own permissions review this function to see how permissions have been set up for the login system itself.

Permissions are stored as an array of arrays (the `$returnValue` variable). Adding new permissions means setting 4 values:

1. The role level, not id
2. The Controller name needs to be checked
3. The Function name to be checked, * is wildcard means all functions in this controller. The wildcard checking has lower priority.
4. Operator, could be =, >, <, >=, <=

By default there are 4 different role levels with increasing levels of permissions:

* `VISITOR` (Not logged in)
* `NORMAL_USER` (Logged in)
* `VIP_USER` (Logged in)
* `ADMINISTRATOR` (Logged in)

### Example Permissions

Let's assume we have a `Ghosts` controller with the following four methods: `show`, `index`, `create` and `delete`. Further, let's assume we want to restrict the `delete` method to `VIP_USER` and `ADMINISTRATOR` users. The `create` method should only be accessible by `ADMINISTRATOR` users. Finally, the `show` and `index` methods should be accessible by anyone, including non-logged in `VISITOR` users:

    $returnValue[] = array(VIP_USER, "Ghosts","delete", ">="); // You must have VIP_USER access or greater to run delete.
    $returnValue[] = array(ADMINISTRATOR, "Ghosts","create", "="); // You must ADMINISTRATOR access to run create.
    $returnValue[] = array(VISITOR, "Ghosts","*", ">="); // Anyone can access the remaining Ghosts controller methods.

## Additional Setup and Information

### Captcha

The constant LOGIN_ATTEMPTING_LIMIT is the login try times before the Captcha occurred. Right now is 2 just for testing purpose. Please at least set it to 5.

There are some other picture generate settings in Captcha.php

### Navigation Bar

You can customize your navigation bar for different user groups. (if you don't have your own navigation bar)
It's in
    Helpers/User_variables_helper.php
	variables_get_navigation()
	    Set the full navigation bar
	variables_get_navigation_permissions()
	    Set the different user group's navigation bar

### Email Notifications

You can set what should be put in the email notification
It's in
    Helpers/User_variables_helper.php
	variables_emails()

### Additional User Columns

If you want additional columns for a user, and you want to keep the User Management page rather than use your own, then you need to modify MVC:

#### Model

The `Models/Users_model.php` file's `read_datatable()` function:
  
* Change what columns should be read for the user list
* The extra search part will process the Advanced Search request from User List page

#### View

View/users/page_index.php

Find the `<table>` tag with id="list_users", add what column you want, have to match the column name from the Model. If its not matching, that `<td>` fileds will be blank.

Those attributes start with "data-" are the annotation of columns, if you want to use the Datatables in your project, feel free to do so.

*  data-source: --- name of the column you are looking for
*  data-orderby-desc
*  data-orderby-asc: --- if has this attribute, will be as default sorting
*  data-filter: --- what columns will be respond by the filter
*  data-icon: ---   if its an icon column. (the value=>icon settings are in create_datatable.js)
*  data-class: ---  change the column's classname
*  data-render: --- change content, need a render function code below. The render function arguments have to be like: fn(data, type, row, meta). The return value will be filled into the <td> tag
*  data-multiselect: --- set the column as multi checkbox column
View/users/form_modal_user.php
The divs userDetailModal and userCreateModal are the form of create or edit an user by the administrator.
If you add any item, please keep the attribute "data-validation", otherwise this item will not be submitted when you hit "save" button.
Those attributes start with "data-" are annotations.
*  data-not-retrive : will be keep empty when getting data
*  data-validation : will be validated when submitting data
*  data-render : Instead of fill-in, the system will call a function. The function format have to be like: fn(itemSelector, value)

#### Controller	

Controllers/Users.php

form_signup()
	What information will be stored when user signup
ajax_create()
	What information will be stored when the administrator create an user
ajax_update()
	Columns of admin-update an user

Becareful, In order to prevent anonymous requests, functions here are better been set (in the permission list). 

### Additional User Groups / Roles

If you need more user groups (roles), you need to change 3 places:

Database
	insert a record to user_groups
	change your admin account's user groups(if you changed the group id of admin user)

Config/constants.php

Helpers/User_variables_helper.php
	Give the new role a customized navigation bar

---- but do not delete the Visitor group, it will be used for the "level = 0" situation.
