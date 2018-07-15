<?php

/*
create database yyyclan;

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER
  ON yyyclan.* TO 'yyyuser'@'localhost'
  IDENTIFIED BY '3yisthebest!';


Run these after generated

 DELIMITER $$
	 CREATE TRIGGER triggerMemberStart
	 BEFORE INSERT ON members
	 FOR EACH ROW
	 BEGIN
		IF NEW.member_start IS NULL THEN
		   SET NEW.member_start = NOW();
		END IF;
	END$$


 DELIMITER $$
	 CREATE TRIGGER triggerUserCreated
	 BEFORE INSERT ON users
	 FOR EACH ROW
	 BEGIN
		IF NEW.user_created IS NULL THEN
		   SET NEW.user_created = NOW();
		END IF;
		IF NEW.user_last_login IS NULL THEN
		   SET NEW.user_last_login = NOW();
		END IF;
	END$$


 DELIMITER $$
	 CREATE TRIGGER triggerTokenCreated
	 BEFORE INSERT ON user_tokens
	 FOR EACH ROW
	 BEGIN
		IF NEW.token_created IS NULL THEN
		   SET NEW.token_created = NOW();
		END IF;
		IF NEW.token_expiry IS NULL THEN
		   SET NEW.token_expiry = NOW();
		END IF;
	END$$


*/

// only to satisfy codeigniter code:
define('BASEPATH', "");
define('ENVIRONMENT', "");
require "application/config/database.php";
require "application/config/constants.php";



$df_hostname = $db['default']['hostname'];
$df_database = $db['default']['database'];
$df_username = $db['default']['username'];
$df_password = $db['default']['password'];


if($_POST)
{

    $hostname = trim(filter_input(INPUT_POST, 'hostname'));
    $databasename = trim(filter_input(INPUT_POST, 'databasename'));
    $username = trim(filter_input(INPUT_POST, 'username'));
    $password = trim(filter_input(INPUT_POST, 'password'));
    $user_table = trim(filter_input(INPUT_POST, 'user_table'));
    $role_table = trim(filter_input(INPUT_POST, 'role_table'));
    $org_table = trim(filter_input(INPUT_POST, 'org_table'));
    $token_table = trim(filter_input(INPUT_POST, 'token_table'));


    define('DB_DSN','mysql:host=' . $hostname. ';dbname='. $databasename.';charset=utf8');
    define('DB_USER',$username);
    define('DB_PASS',$password);

    try {
        $db = new PDO(DB_DSN, DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die();
    }

    $query = "DROP TABLE IF EXISTS events_members;";
    $query .= "DROP TABLE IF EXISTS events;";
    $query .= "DROP TABLE IF EXISTS event_categories;";
    $query .= "DROP TABLE IF EXISTS members;";
    $query .= "DROP TABLE IF EXISTS tags;";
    $query .= "DROP TABLE IF EXISTS tag_categories;";

    $query .= "DROP TABLE IF EXISTS ".$token_table. ";";
    $query .= "DROP TABLE IF EXISTS ".$user_table .";";
    $query .= "DROP TABLE IF EXISTS ".$role_table. ";";
    $query .= "DROP TABLE IF EXISTS ".$org_table. ";";

    $query .= "ALTER DATABASE " . $databasename . " CHARACTER SET utf8 COLLATE utf8_unicode_ci;";



    $statement = $db->prepare($query);
    $statement->execute();
    echo $query;
    echo "<br/>";


    // user_token: generated after login, used to decode the token
    // user_token_key: used to match the token from client side


    /*
    user status:
    0: inactive
    1: active
    2: reset password
    */
    $query = "CREATE TABLE ". $user_table ." (
		user_id 		INT(11)	AUTO_INCREMENT PRIMARY KEY,
		user_email		VARCHAR(50) NOT NULL,
		user_password	VARCHAR(255),
		user_created 	DATETIME,
		user_active		BOOLEAN  DEFAULT 1,
		user_group_id	INT(11) DEFAULT 1,
		organization_id	INT(11) DEFAULT 0,
		user_last_login DATETIME,
		is_deleted		BOOLEAN DEFAULT 0
	);";

    $query .= "CREATE TABLE ". $role_table ." (
		user_group_id 		INT(11)	PRIMARY KEY,
		user_group_name		VARCHAR(50),
		user_group_level	INT(11)
	);";

    $query .= "CREATE TABLE ". $org_table ." (
		organization_id 		INT(11) PRIMARY KEY,
		organization_name		VARCHAR(64),
		organization_fullname	VARCHAR(64),
		organization_prefix		VARCHAR(64),
		organization_logo		VARCHAR(64)
	);";

    $query .= "CREATE TABLE ". $token_table ." (
		token_id 		INT(11) AUTO_INCREMENT PRIMARY KEY,
		user_id			INT(11),
		token			VARCHAR(350),
		token_key		VARCHAR(64) ,
		token_type		INT(2),
		token_created	DATETIME,	
		token_expiry	DATETIME,	
		UNIQUE(token_key)
	);";

    /*
    ================================== functional tables
    */

    // tag_categories
    $query .= "CREATE TABLE tag_categories (
            tag_category_id	INT(11) AUTO_INCREMENT	PRIMARY KEY,
            tag_category_name	VARCHAR(50),
            tag_category_column	VARCHAR(50)
        );";

    // tags
    $query .= "CREATE TABLE tags (
            tag_id	INT(11) AUTO_INCREMENT	PRIMARY KEY,
            tag_name	VARCHAR(50),
            tag_description	TEXT,
            tag_value	INT(11),
            tag_picture VARCHAR(255),
            tag_category_id	INT(11)
        );";

    // members
    $query .= "CREATE TABLE members (
            member_id 		INT(11)	AUTO_INCREMENT PRIMARY KEY,
            user_id		    INT(11),
            member_code 	VARCHAR(255),
            member_gamename	VARCHAR(255),
            member_nickname VARCHAR(255),
            member_picture 	VARCHAR(255),
            member_description 	TEXT,
            member_start 	DATETIME,
            member_end 	    DATETIME,
            member_status   INT(11) DEFAULT 0,
            member_perks    TEXT,
            member_position TEXT,
            member_shirt_number INT(11) DEFAULT 0,
            organization_id	INT(11) DEFAULT 0,
            member_kpi      INT(11),
            member_medals	TEXT,
            member_tagvalue INT(11),
            member_games	TEXT,
            is_deleted		BOOLEAN DEFAULT 0
        );";


    // event_categories
    $query .= "CREATE TABLE event_categories (
            event_category_id INT(11) AUTO_INCREMENT  PRIMARY KEY,
            event_category_name	  VARCHAR(50),
            organization_id	INT(11) DEFAULT 0
        );";

    // events
    $query .= "CREATE TABLE events (
            event_id	      INT(11) AUTO_INCREMENT	PRIMARY KEY,
            event_category_id INT(11),
            event_location	  VARCHAR(255),
            event_description TEXT,
            event_time        DATETIME,
            event_status      INT(11),
            organization_id	INT(11) DEFAULT 0
        );";

    // events_members
    $query .= "CREATE TABLE events_members (
            event_id	      INT(11),
            member_id	      INT(11),
            description       TEXT,
            attendance        BOOLEAN,
            score             INT(11),
            PRIMARY KEY (event_id, member_id)
        );";

    $statement = $db->prepare($query);
    $statement->execute();

    echo $query;
    echo "<br/><br/>";


    $query = "ALTER TABLE members
				ADD FOREIGN KEY (user_id) REFERENCES ". $user_table ."(user_id)
				ON DELETE CASCADE;";

    $query .= "ALTER TABLE tags
				ADD FOREIGN KEY (tag_category_id) REFERENCES tag_categories(tag_category_id)
				ON DELETE CASCADE;";

    $query .= "ALTER TABLE events
				ADD FOREIGN KEY (event_category_id) REFERENCES event_categories(event_category_id)
				ON DELETE CASCADE;";

    $query .= "ALTER TABLE events_members
				ADD FOREIGN KEY (event_id) REFERENCES events(event_id)
				ON DELETE CASCADE;";

    $query .= "ALTER TABLE events_members
				ADD FOREIGN KEY (member_id) REFERENCES members(member_id)
				ON DELETE CASCADE;";

    /*
    ==================================
    */

    $query .= "ALTER TABLE ".$token_table ."
				ADD FOREIGN KEY (user_id) REFERENCES ". $user_table ."(user_id)
				ON DELETE CASCADE;";


    $statement = $db->prepare($query);
    $statement->execute();


    echo $query;
    echo "<br/><br/>";

    // insert the super admin user
    $query =  "INSERT INTO " . $org_table."(organization_id, organization_name, organization_fullname, organization_prefix) VALUES(1, 'Black Ram Head', '黑羊头', 'YYY_');";
    $query .=  "INSERT INTO " . $org_table."(organization_id, organization_name, organization_fullname, organization_prefix) VALUES(2, 'Black Lee Head', '黑鸽头', 'λλλ_');";

    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(0, 'Visitor', 0);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(1, 'Intern', 1);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(2, 'Recruit', 2);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(3, 'Veteran', 3);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(4, 'Vanguard', 4);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(5, 'Anvil Guard', 5);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(6, 'Praetorian', 6);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(7, 'Vice Captain', 7);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(8, 'Captain', 8);";
    $query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(9, 'Administrator', 9);";

    $query .=  "INSERT INTO " . $user_table."(user_email, user_password, user_group_id, organization_id) VALUES('admin@yyyclan.com', :adminPassword, 9, 1);";
    $query .=  "INSERT INTO " . $user_table."(user_email, user_password, user_group_id, organization_id) VALUES('test@yyyclan.com', :adminPassword, 1, 1);";

    /*
    ================================== functional tables
    */
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(1, '号码旗', 'member_shirt_number');";
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(2, '技能', 'member_perks');";
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(3, '职业', 'member_position');";
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(4, '游戏', 'member_games');";
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(5, '勋章', 'member_medals');";
    $query .=  "INSERT INTO tag_categories(tag_category_id, tag_category_name, tag_category_column) VALUES(6, '会员状态', 'member_status');";



    $query .=  "INSERT INTO tags(tag_id, tag_name, tag_picture, tag_category_id) VALUES(1, 'Infantry', '', 3);";
    $query .=  "INSERT INTO tags(tag_id, tag_name, tag_picture, tag_category_id) VALUES(2, 'Cavalry', '', 3);";
    $query .=  "INSERT INTO tags(tag_id, tag_name, tag_picture, tag_category_id) VALUES(3, 'Archer', '', 3);";

    $query .=  "INSERT INTO tags( tag_name, tag_picture, tag_category_id) VALUES( '未登记', '', 6);";
    $query .=  "INSERT INTO tags( tag_name, tag_picture, tag_category_id) VALUES( '在岗', '', 6);";
    $query .=  "INSERT INTO tags( tag_name, tag_picture, tag_category_id) VALUES( '沉寂', '', 6);";
    $query .=  "INSERT INTO tags( tag_name, tag_picture, tag_category_id) VALUES( '退队', '', 6);";



    $query .=  "INSERT INTO event_categories(event_category_id, event_category_name, organization_id) VALUES(1, '平时训练', 1);";
    $query .=  "INSERT INTO event_categories(event_category_id, event_category_name, organization_id) VALUES(2, '正规赛事', 1);";

    /*
    ================================== functional tables
    */
    $statement = $db->prepare($query);
    $statement->bindValue(':adminPassword' , password_hash('yyy123456', PASSWORD_DEFAULT));
    $statement->execute();
}

?>
<!doctype html>
<html lang = "en">
<head>
    <meta charset="utf-8"/>
    <title>Ace CI User Management</title>

    <style>
        form p{line-height: 20px;}
        form p label{display: inline-block;width: 250px;}


    </style>
</head>
<body>
<form action="" method="post">

    <p><label>database hostname:</label> <input type="text" readonly name="hostname" value="<?=$df_hostname?>"/></p>
    <p><label>database name:</label> <input type="text" readonly name="databasename" value="<?=$df_database?>"/></p>
    <p><label>database username:</label> <input type="text" readonly name="username" value="<?=$df_username?>"/></p>
    <p><label>database password:</label> <input type="text" readonly name="password" value="<?=$df_password?>"/></p>

    <p><label>Name of the User Table:</label> <input type="text" readonly name="user_table" value="<?=TABLE_USER?>"/></p>
    <p><label>Name of the Role Table:</label> <input type="text" readonly name="role_table" value="<?=TABLE_USER_GROUP?>"/></p>
    <p><label>Name of the Organization Table:</label> <input type="text" readonly name="org_table" value="<?=TABLE_ORG?>"/></p>
    <p><label>Name of the Token Table:</label> <input type="text" readonly name="token_table" value="<?=TABLE_TOKEN?>"/></p>


    <button>Generate</button>
</form>
</body>
</html>

