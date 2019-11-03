create database if not exists finder;

use finder;

create table if not exists authors (id_author int UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(20) NOT NULL,
PRIMARY KEY (id_author));

create table if not exists books (id_book int  NOT NULL AUTO_INCREMENT, 
author int UNSIGNED NOT NULL, 
name VARCHAR(50) NOT NULL,
date_create date NOT NULL,
key_words text,
decription text,
FOREIGN KEY (author) REFERENCES authors(id_author),
PRIMARY KEY (id_book));



