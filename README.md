# SETTING THE ENVIRONMENT

## INSTALLATIONS

I will assume that you have already installed PHP and MongoDB in your computer.
I used an old MacOS (3,3 GHz Quad-Core Intel Core i7) with macOS Mointerrey version 12.7.6
I have XAMPP PHP for MacOS : PHP 8.2.4

0. Install MongoDB extension
(Working with MacOS)
sudo /Applications/XAMPP/xamppfiles/bin/pecl install mongodb
Edit php.ini at the end of section "Dynamic Extensions" :
extension=mongodb.so

1. Navigate to project root:
cd /path/to/your/project

2. Initialize Composer:
composer init

3. Install MongoDB library:
composer require mongodb/mongodb

4. Install dependencies:
composer install

5. Verify MongoDB extension:
php -m | grep mongodb

6. If not shown, add to php.ini:
echo "extension=mongodb.so" >> /path/to/your/php.ini

7. Restart web server:
sudo apachectl restart

8. Edit web/index.php
// Load Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';


## ROADMAP STAGE 1

1. Update config/settings.ini:
Replace MySQL settings with MongoDB connection details:
[database]
driver = mongodb
host = 127.0.0.1
port = 27017
dbname = optics
user = root
password = 12345

2. Modify db.inc.php
<?php
$settings = parse_ini_file('settings.ini', true);
$dbSettings = $settings['database'];
$uri = "mongodb://{$dbSettings['host']}:{$dbSettings['port']}";
$client = new MongoDB\Client($uri);
$db = $client->selectDatabase($dbSettings['dbname']);
?>

3. Modify lib/base/Model.php:
    Update the constructor to use MongoDB instead of PDO.

    PDO is not suitable for MongoDB for several reasons:

    1) PDO is designed for relational databases, while MongoDB is a NoSQL database. PDO supports 12 different database drivers, but MongoDB is not among them.
    2) MongoDB requires its own specific driver for PHP, which is separate from PDO. The official MongoDB PHP driver provides a low-level API that integrates with MongoDB's native libraries.
    3) The MongoDB PHP driver offers features specifically tailored for MongoDB, such as BSON encoding/decoding and command execution, which PDO does not provide.
    4) Using the MongoDB-specific driver allows for better performance and functionality compared to using a generic database abstraction layer like PDO.
    
    To work with MongoDB in PHP, you should use the official MongoDB PHP driver and, optionally, the MongoDB PHP library for a higher-level API.

    <?php
    class Model {
        protected $collection;
        protected static $db;

        public function __construct() {
            $this->connect();
            $this->collection = self::$db->selectCollection(strtolower(get_class($this)));
        }

        protected function connect() {
            if (!self::$db) {
                require_once(ROOT_PATH . '/config/db.inc.php');
                self::$db = $db;
            }
        }

        public function find($criteria = []) {
            return $this->collection->find($criteria)->toArray();
        }

        public function findOne($criteria = []) {
            return $this->collection->findOne($criteria);
        }

        public function insert($document) {
            return $this->collection->insertOne($document);
        }

        public function update($criteria, $update) {
            return $this->collection->updateMany($criteria, ['$set' => $update]);
        }

        public function delete($criteria) {
            return $this->collection->deleteMany($criteria);
        }
    }

4. Use environment.inc.php for database initialization. Use it to to create and feed the database.

5. Start creating your models OR Update existing models:
    Create/Modify your models to work with MongoDB instead of MySQL.

6. Update controllers:
    Adjust controllers to work with the new MongoDB models.

7. Test the setup:
    Create a simple test route and controller to verify the MongoDB connection.




# BELOW THE ORIGINAL README OF THE FRAMEWORK AUTHOR

# PHP initial Project
Main structure of php project. Folders / files:
- **app**
  - **controllers**
  - **models**
  - **views**
- **config**
- **lib**
  - **base**
- **web**

### Usage

The web/index.php is the heart of the system.
This means that your web applications root folder is the “web” folder.

All requests go through this file and it decides how the routing of the app
should be.
You can add additional hooks in this file to add certain routes.

### Project Structure

The root of the project holds a few directories:
**/app** This is the folder where your magic will happen. Use the views, controllers and models folder for your app code.
**/config** this folder holds a few configuration files. Currently only the connection to the database.
**/lib** This is where you should put external libraries and other external files.
**/lib/base** The library files. Don’t change these :)
**/web** This folder holds files that are to be “downloaded” from your app. Stylesheets, javascripts and images used. (and more of course)

The system uses a basic MVC structure, with your web app’s files located in the
“app” folder.

#### app/controllers
Your application’s controllers should be defined here.

All controller names should end with “Controller”. E.g. TestController.
All controllers should inherit the library’s “Controller” class.
However, you should generally just make an ApplicationController, which extends
the Controller. Then you can defined beforeFilters etc in that, which will get run
at every request.

#### app/models
Models handles database interaction etc.

All models should inherit from the Model class, which provides basic functionality.
The Model class handles basic functionality such as:

Setting up a database connection (using PDO)
fetchOne(ID)
save(array) → both update/create
delete(ID)
app/views
Your view files.
The structure is made so that having a controller named TestController, it looks
in the app/views/test/ folder for it’s view files.

All view files end with .phtml
Having an action in the TestController called index, the view file
app/views/test/index.phtml will be rendered as default.

#### config/routes.php
Your routes around the system needs to be defined here.
A route consists of the URL you want to call + the controller#action you want it
to hit.

An example is:
$routes = array(
‘/test’ => ‘test#index’ // this will hit the TestController’s indexAction method.
);

#### Error handling
A general error handling has been added.

If a route doesn’t exist, then the error controller is hit.
If some other exception was thrown, the error controller is hit.
As default, the error controller just shows the exception occured, so remember
to style the error controller’s view file (app/views/error/error.phtml)


### Utilities
- [PHP Developers Guide](https://www.php.net/manual/en/index.php).
- .gitignore file configuration. [See Official Docs](https://docs.github.com/en/get-started/getting-started-with-git/ignoring-files).
- Git branches. [See Official Docs](https://git-scm.com/book/en/v2/Git-Branching-Branches-in-a-Nutshell).
