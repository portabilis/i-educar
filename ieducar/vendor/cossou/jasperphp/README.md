# JasperReports for PHP 

Package to generate reports with [JasperReports](http://community.jaspersoft.com/project/jasperreports-library) library through [JasperStarter](http://jasperstarter.sourceforge.net/) command-line tool.

##Introduction

This package aims to be a solution to compile and process JasperReports (.jrxml & .jasper files). 

###Why?

Did you ever had to create a good looking Invoice with a lot of fields for your great web app? 

I had to, and the solutions out there were not perfect. Generating *HTML* + *CSS* to make a *PDF*? WTF? That doesn't make any sense! :)

Then I found **JasperReports** the best open source solution for reporting.

###What can I do with this?

Well, everything. JasperReports is a powerful tool for **reporting** and **BI**. 

**From their website:**

> The JasperReports Library is the world's most popular open source reporting engine. It is entirely written in Java and it is able to use data coming from any kind of data source and produce pixel-perfect documents that can be viewed, printed or exported in a variety of document formats including HTML, PDF, Excel, OpenOffice and Word.

I recommend you to use [iReports designer](http://community.jaspersoft.com/project/ireport-designer) to build your reports, connect it to your datasource (ex: MySQL), loop thru the results and output it to PDF, XLS, DOC, RTF, ODF, etc.

*Some examples of what you can do:*

* Invoices
* Reports
* Listings

##Examples

###The *Hello World* example.

Go to the examples directory in the root of the repository (`vendor/cossou/jasperphp/examples`).
Open the `hello_world.jrxml` file with iReport or with your favorite text editor and take a look at the source code.

#### Compiling

First we need to compile our `JRXML` file into a `JASPER` binary file. We just have to do this one time. 

**Note:** You don't need to do this step if you are using *iReport Designer*. You can compile directly within the program.

	JasperPHP::compile(base_path() . '/vendor/cossou/jasperphp/examples/hello_world.jrxml')->execute();

This commando will compile the `hello_world.jrxml` source file to a `hello_world.jasper` file.

**Note:** If you are using Laravel 4 run `php artisan tinker` and copy & paste the command above.

####Processing

Now lets process the report that we compile before: 

	JasperPHP::process(
		base_path() . '/vendor/cossou/jasperphp/examples/hello_world.jasper', 
		false, 
		array("pdf", "rtf"), 
		array("php_version" => phpversion())
	)->execute();

Now check the examples folder! :) Great right? You now have 2 files, `hello_world.pdf` and `hello_world.rtf`.

Check the *API* of the  `compile` and `process` functions in the file `src/JasperPHP/JasperPHP.php` file.

###Advanced example

TODO.

##Requirements

* Java JDK 1.6
* PHP [exec()](http://php.net/manual/function.exec.php) function
* [optional] [Mysql Connector](http://dev.mysql.com/downloads/connector/j/) (if you want to use database) 
* [optional] [iReports](http://community.jaspersoft.com/project/ireport-designer) (to draw and compile your reports) 


##Installation

###Java

Check if you already have Java installed:
```java	
	$ java -version
	java version "1.6.0_51"
	Java(TM) SE Runtime Environment (build 1.6.0_51-b11-457-11M4509)
	Java HotSpot(TM) 64-Bit Server VM (build 20.51-b01-457, mixed mode)
```
If you get:
	
	command not found: java 

Then install it with: (Ubuntu/Debian)

	$ sudo apt-get install default-jdk

Now run the `java -version` again and check if the output is ok.

###Composer

Install [Composer](http://getcomposer.org) if you don't have it.

Now in your `composer.json` file add:
```javascript
{
    "require": {
	"cossou/jasperphp": "dev-master",
    }
}
```
	
And the just run:

	composer update

and thats it.	

###Using Laravel 4?

Add to your `app/config/app.php` providers array:
```php
'JasperPHP\JasperPHPServiceProvider',
```	
Now you will have the `JasperPHP` alias available.

###MySQL

If you want to use MySQL in your report datasource, please add the `JAR` to the `/src/JasperStarter/jdbc/` directory. Download it [here](http://dev.mysql.com/downloads/connector/j/).

##Performance

Depends on the complexity, amount of data and the resources of your machine (let me know your use case).

I have a report that generates a *Invoice* with a DB connection, images and multiple pages and it takes about **3/4 seconds** to process. I suggest that you use the [Laravel 4 Queue](#) feature.


##Thanks

Thanks to [Cenote GmbH](http://www.cenote.de/) for the [JasperStarter](http://jasperstarter.sourceforge.net/) tool.

##Questions?

Drop me a line on Twitter [@cossou](https://twitter.com/cossou).


##License

MIT
