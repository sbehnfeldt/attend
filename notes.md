# Attend

Attendance scheduling application

# Tools and Technologies

* PHP
* Propel ORM
* phing task runner
* Composer dependency manager
* phinx database migration tool
* jQuery front end library

## Composer

Use `php composer.phar require <dependency>` or `composer require <dependency>`
to add <dependency> to the composer.json file.

## Phinx

For database migrations.

After using Composer to install the phinx PHP dependency,
create the database and the associated user,
then execute the database migrations:

```shell
$> php composer.phar require robmorgan/phinx
$> mysql -u root -p -e "CREATE DATABASE attend;"
$> mysql -u root -p -e "CREATE USER 'attend'@'localhost' IDENTIFIED BY 'attend'; GRANT Alter ON *.* TO 'attend'@'localhost'; GRANT Create ON *.* TO 'attend'@'localhost'; GRANT Create view ON *.* TO 'attend'@'localhost'; GRANT Delete ON *.* TO 'attend'@'localhost'; GRANT Drop ON *.* TO 'attend'@'localhost'; GRANT Grant option ON *.* TO 'attend'@'localhost'; GRANT Index ON *.* TO 'attend'@'localhost'; GRANT Insert ON *.* TO 'attend'@'localhost'; GRANT References ON *.* TO 'attend'@'localhost'; GRANT Select ON *.* TO 'attend'@'localhost'; GRANT Show view ON *.* TO 'attend'@'localhost'; GRANT Trigger ON *.* TO 'attend'@'localhost'; GRANT Update ON *.* TO 'attend'@'localhost'; GRANT Alter routine ON *.* TO 'attend'@'localhost'; GRANT Create routine ON *.* TO 'attend'@'localhost'; GRANT Create temporary tables ON *.* TO 'attend'@'localhost'; GRANT Execute ON *.* TO 'attend'@'localhost'; GRANT Lock tables ON *.* TO 'attend'@'localhost'; GRANT Grant option ON *.* TO 'attend'@'localhost';"
$> .\vendor\bin\phinx migrate
```

## Propel

Install the Propel dependency  
`$> php composer.phar require propel ~2.0@beta`

### Propel Config File

_propel/propel.json_

```json
{
    "propel": {
        "database": {
            "connections": {
                "attend": {
                    "adapter": "mysql",
                    "classname": "Propel\\Runtime\\Connection\\ConnectionWrapper",
                    "dsn": "mysql:host=localhost;dbname=attend",
                    "user": "attend",
                    "password": "attend",
                    "attributes": []
                }
            }
        },
        "runtime": {
            "defaultConnection": "attend",
            "connections": [
                "attend"
            ]
        },
        "generator": {
            "defaultConnection": "attend",
            "namespaceAutoPackage": false,
            "connections": [
                "attend"
            ]
        }
    }
}

```

### Schema.xml

Write the schema.xml file describing the database schema. If using an existing database, it can be generated
automatically.

```shell
$> .\vendor\bin\propel reverse --config-dir=../ --output-dir=propel --schema-name=attend-schema --database-name=attend --namespace=Attend\Database "mysql:host=localhost;dbname=attend;user=attend;password=attend" 
$> .\vendor\bin\propel reverse --config-dir=../ --output-dir=propel --schema-name=attend-schema --database-name=attend --namespace=Attend\Database attend
```

* --config-dir: directory containing the propel.json or similar config file
* --output-dir: directory to create the output xml file
* --schema-name: name of the xml file to generate (does not require the extension, but MUST end in 'schema.xml')
* --namespace: value for the "namespace" attribute of &lt;database&gt; and &lt;table&gt; elements in generated schema
  file.
* --database-name: value for the "name" attribute of the &lt;database&gt; element in generated schema file.

**TODO:** Delete any `&lt;table&gt;` elements that should not have model classes generated (eg, phinxlog)  
**TODO:** Change the value of the phpName attribute of every table element to singular.  
**TODO:** Correct value of namespace attributes in database and table elements, as necessary

### SQL and Schema Map

```shell
.\vendor\bin\propel model:build --config-dir=propel --schema-dir=propel --output-dir=classes/database
```

* --config-dir: directory containing the propel.json or similar config file
* --schema-dir: directory containing the "*schema.xml" input files
* --output-dir: directory to create the sql and map files.

### Model Classes

```shell
$> .\vendor\bin\propel model:build --config-dir=propel --schema-dir=propel --output-dir=classes
```

## Phinx

```shell
$> vendor/bin/phinx init --format=yml
$> vendor/bin/phinx create <ClassName>
$> vendor/bin/phinx migrate
$> vendor/bin/phinx rollback
```

cls && vendor\bin\propel help model:build
cls && vendor\bin\propel model:build --config-dir=../ --schema-dir=generated-reversed-database
--output-dir=classes/Attend/Database

cls && vendor\bin\propel help convert
; cls && vendor\bin\propel convert --config-dir=generated-reversed-database --output-dir=lib
cls && vendor\bin\propel convert --config-dir=../ --output-dir=lib

; Use specific version of PHP to run composer.
; Based on https://stackoverflow.com/questions/32750250/tell-composer-to-use-different-php-version
c:\php\php-7.4.25\php.exe c:\ProgramData\ComposerSetup\bin\composer.phar install

# Architecture

* .htaccess file directs requests to index.php front controller.
* application is bootstrapped
    * ensure required directories exist
    * read config file
    * start session
