# News App

News App web app to list, add, delete
the most important news in your area.

Developed with PHP-8. (**Developing**)

**CRUD Web App**

### Table of contents 📃

- [News App](#news-app)
    - [Table of contents 📃](#table-of-contents-)
    - [Starting 🚀](#starting-)
        - [Pre-requirements 📋](#pre-requirements-)
        - [DataBase-Management-System](#DBMS)
        - [Installation 🔧](#installation-)
    - [Use 📌](#use-)
    - [Built with 🛠️](#built-with-️)

## Starting 🚀

### Pre-requirements 📋

* [Git](https://git-scm.com/)
* [PHP-8](https://www.php.net/downloads.php)
* [MySQL](https://www.mysql.com/downloads/)
* [Composer](https://getcomposer.org/)

### Installation 🔧

Local installation:

```bash
# Clone this repository
# linux (ubuntu) /var/www/html/
# windows: 
#  - for laragon in www folder
#  - for xammp in htdocs folder
$ git clone https://github.com/JefferGonzalez/news-app

# Change directory to the project path
$ cd news-app
```

Setup:

```bash
# Install dependencies
$ composer install
```

### DBMS

In your DataBase Management System (**MYSQL**):

Copy and executed the script file of folder (Url **'app/database/script.sql'**)

### .env file setup

```bash
Create an .env file and copy all content of .env.example

Then update .env file with you database credentials
```

## Use 📌

Open your browser and go to the url of your project

```bash
http://localhost/news-app/
```

## Built with 🛠️

* [Apache](https://www.apache.org/) - Web Server
* [PHP](https://www.php.net/) - Programming Languaje
    * [Composer](https://getcomposer.org/) - Dependency Management for PHP
* [REACT.JS](https://beta.reactjs.org/) - Frontend Library
    * [Vite](https://vitejs.dev/) - Frontend Tooling
    * [PNPM](https://pnpm.io/) - Package Manager
    * [Bootstrap](https://getbootstrap.com/) - CSS Framework
    * [React-Bootstrap](https://react-bootstrap.github.io/) - Bootstrap for React
* [MYSQL](https://www.mysql.com) - DataBase Management System
  * [PHPMyAdmin](https://www.phpmyadmin.net/) - Visual Tool for MYSQL
  * [DBDiagram](https://dbdiagram.io/) - Relational Database Diagram Design Tool