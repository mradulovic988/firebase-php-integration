<!-- PROJECT LOGO -->
<br />
<p align="center">

  <h3 align="center">Firebase PHP Integration</h3>

  <p align="center">
    In this example I'll show you how to integrate Firebase in PHP 7.x and create a simple CRUD function with which you can perform database manipulation.
    <br />
  </p>
</p>


<!-- ABOUT THE PROJECT -->
## About The Project

Firebase is a real-time communication service providing real-time data flow for chat, mobile and web applications. It provides several real time services including Cloud messaging, auth systems, Firebase PHP database, Notification systems, Storage and Firebase Hosting, making its platform a complete plethora of tools and services for developing high quality apps.


<!-- Firebase VS MySQL -->
## Firebase VS MySQL

Firebase is literally different than traditional databases like MySQL and stores data in the form of documents. These documents can be manipulated in real-time in cross platform mediums. MySQL is a relational database which works with `key->value` concept and create relations with different set of data.

These relations are then used for data transactions. MySQL lacks the real-time data transition concept and requires so many work to create REST APIs.

On the other hand Firebase database provides different platforms like Android, iOS and Web for creating quick APIs. Basically Firebase is a hierarchical data structure i.e it’s just like a JSON tree in the cloud.

Firebase has the concept of keys, which are the names of the nodes that you store data under. You could somehow compare them to the primary keys of a relational database but there is no concept of a managed foreign key in it.


<!-- Integrate PHP With Firebase -->
## Integrate PHP With Firebase

Firebase has an awesome API which supports implementation in different languages. For using PHP with Firebase, it provides a complete PHP package which you can integrate for working with Firebase. In this application, i will use [kreait/firebase-php](https://github.com/kreait/firebase-php) which is recommended by Firebase also.

The recommended way to install the Firebase Admin SDK is with Composer. Composer is a dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them into your project.
* composer
```
composer require kreait/firebase-php ^4.17.0
```

Alternatively, you can specify the Firebase Admin SDK as a dependency in your project’s existing composer.json file:
```
{
  "require": {
     "kreait/firebase-php": "^4.17.0"
  }
}
```

After installing, you need to require Composer’s autoloader:
```
<?php
require_once './vendor/autoload.php';
```

<!-- Create Connection With Firebase -->
## Create Connection With Firebase

Once completing the package installation and adding the autolader to the PHP file, you can then create a connection with Firebase using the secret file downloaded from the console. You need to pass out the URL of that file in `ServiceAccount::fromJsonFile()` method. Secondly, find the Firebase application URL.


```
<?php
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Here you need to export your JSON secret file
$serviceAccount = \Kreait\Firebase\ServiceAccount::fromJsonFile(__DIR__ . '/secret/path_to_the_json_file.json');

$firebase = (new Factory)
  ->withServiceAccount($serviceAccount)
  ->create();

$database = $firebase->getDatabase();
```

Now at this time when you do `var_dump($database);` you will see the complete database object returned by the Firebase on your browser screen.



<!-- Create Crud Class in Crud.php -->
## Create Crud Class in Crud.php

The next thing I will do is the creation of new file called `Crud.php` and declare a `Crud` class in it. The class will contain a constructor in which i will create a connection of PHP with Firebase API and initialize it. After that I’ll get the database created in firebase.

```
public function __construct() {
    $acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/path_to_the_secred_json_file.json');
    $firebase = (new Factory) -> withServiceAccount($acc)->create();

    $this->database = $firebase->getDatabase();
  }
```

Now to create PHP CRUD functions to manipulate database, I’ll create separate methods for each use case. But first let’s create two more properties outside of the constructor which contains the database name

```
protected $database;
protected $dbname = 'crud';
```

Now let’s create a get() method to retrieve the data. It will contain the CrudID to fetch specific Crud record. The method will also check if the CrudID is not available or set, in case of that will return false. While if founds the ID to be appropriately set, will retrieve the value successfully.
```
public function get(int $CrudID = NULL){
    if (empty($CrudID) || !isset($CrudID)) { return false; }

    if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($CrudID)) {
      return $this->database->getReference($this->dbname)->getChild($CrudID)->getValue();
    } else {
      return false;
    }
  }
```

Inside the `insert()` method, I’ll pass the array of data because it can contain single or multiple data. While if the data is already available for the specific CrudID, it will update the existing one.

```
public function insert(array $data) {
    if (empty($data) || !isset($data)) {return false; }

    foreach ($data as $key => $value) {
        //You can set push() in order to get generated keys. Read more in docs.
      $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
    }
    return true;
  }
```

Now i’ll create a delete() function which will have CrudID as a parameter. The ID will be validated if is set, then `remove()` method will remove the data.

```
public function delete(int $CrudID) {
    if (empty($CrudID) || !isset($CrudID)) { return false; }

    if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($CrudID)) {
      $this->database->getReference($this->dbname)->getChild($CrudID)->remove();
      return true;
    } else {
      return false;
    }
  }
```


<!-- Complete Crud class -->
## Complete Crud class

The complete Crud class will be as follows:
```
require_once './vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Crud {

  protected $database;
  protected $dbname = 'Cruds'; // Name of the Firebase table

  public function __construct() {
    $acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/path_to_the_secred_json_file.json');
    $firebase = (new Factory) -> withServiceAccount($acc)->create();

    $this->database = $firebase->getDatabase();
  }

  /**
   * @param int|NULL $CrudID
   * @return bool|mixed
   * @throws \Kreait\Firebase\Exception\ApiException
   *
   * Getting data from the Firebase
   */
  public function get(int $CrudID = NULL){
    if (empty($CrudID) || !isset($CrudID)) { return false; }

    if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($CrudID)) {
      return $this->database->getReference($this->dbname)->getChild($CrudID)->getValue();
    } else {
      return false;
    }
  }

  /**
   * @param array $data
   * @return bool
   * @throws \Kreait\Firebase\Exception\ApiException
   *
   * Adding data inside the Firebase
   */
  public function insert(array $data) {
    if (empty($data) || !isset($data)) {return false; }

    foreach ($data as $key => $value) {
        //You can set push() in order to get generated keys. Read more in docs.
      $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
    }
    return true;
  }

  /**
   * @param int $CrudID
   * @return bool
   * @throws \Kreait\Firebase\Exception\ApiException
   *
   * Deleting data from the Firebase
   */
  public function delete(int $CrudID) {
    if (empty($CrudID) || !isset($CrudID)) { return false; }

    if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($CrudID)) {
      $this->database->getReference($this->dbname)->getChild($CrudID)->remove();
      return true;
    } else {
      return false;
    }
  }
}

$crud = new Crud();
```

Now let’s test the class. Try to apply methods and check the Firebase database that it updates or not. First initialize the class:
Now let’s insert some data via `insert()` method:
```
var_dump($crud->insert([
  '1' => 'Input 1',
  '2' => 'Input 2',
  '3' => 'Input 3'
]));
```

Get data:

```
var_dump($crud->get(1));
```

Or delete data:
```
var_dump($crud->delete(3));
```

<!-- Conclusion -->
## Conclusion
So you can see how I built a basic CRUD application using PHP and Firebase by creating a simple class. Obviously you can extend it and can create something exciting as per the requirements.


<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch 
3. Commit your Changes 
4. Push to the Branch 
5. Open a Pull Request



<!-- LICENSE -->
## License

Distributed under GNU General Public License v3.0. See `LICENSE` for more information.



<!-- CONTACT -->
## Contact

<p>Author: Marko Radulovic </p>
<p>Find me on: <a href="https://www.linkedin.com/in/marko-radulovic/" target="_blank">LinkedIn</a></p>
<p>Email: <a href="mailto:upss070288@gmail.com">upss070288@gmail.com</a></p>


<small>Thanks Shahroze Nawaz on help.</small>


