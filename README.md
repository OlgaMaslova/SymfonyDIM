# Project Showroom
========

This is an educational project created during Symfony class of Licence Pro DIM course. The objective is to create a web application of TV shows.

## Requirements
- PHP 5.5.9 or higher
- Composer (https://getcomposer.org/)

## Installation

Clone the project to your project's folder locally:

`git clone https://github.com/OlgaMaslova/SymfonyDIM.git`

Make composer install the project's dependencies into vendor/

`composer install`

Create database with the name from parameters.yml->'database_name'

`bin/console doctrine:database:create`

## Usage

Just execute this command in the project's folder:

`bin/console server:start`
