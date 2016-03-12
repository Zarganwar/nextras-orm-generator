CREATE DATABASE generator_test;

CREATE TABLE author (
	id int NOT NULL AUTO_INCREMENT,
	name varchar(30) NOT NULL,
	web varchar(100) NOT NULL,
	born date DEFAULT NULL,
	PRIMARY KEY(id)
) AUTO_INCREMENT=13;

CREATE TABLE tag (
	id int NOT NULL AUTO_INCREMENT,
	name varchar(20) NOT NULL,
	PRIMARY KEY (id)
) AUTO_INCREMENT=25;

CREATE TABLE book (
	id int NOT NULL AUTO_INCREMENT,
	author_id int NOT NULL,
	translator_id int,
	title varchar(50) NOT NULL,
	next_volume int,
	PRIMARY KEY (id),
	CONSTRAINT book_author FOREIGN KEY (author_id) REFERENCES author (id),
	CONSTRAINT book_translator FOREIGN KEY (translator_id) REFERENCES author (id),
	CONSTRAINT book_volume FOREIGN KEY (next_volume) REFERENCES book (id)
) AUTO_INCREMENT=5;

CREATE INDEX book_title ON book (title);

CREATE TABLE book_tag (
	book_id int NOT NULL,
	tag_id int NOT NULL,
	PRIMARY KEY (book_id, tag_id),
	CONSTRAINT book_tag_tag FOREIGN KEY (tag_id) REFERENCES tag (id),
	CONSTRAINT book_tag_book FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE
);

CREATE TABLE book_tag_alt (
	book_id int NOT NULL,
	tag_id int NOT NULL,
	state varchar(30),
	PRIMARY KEY (book_id, tag_id),
	CONSTRAINT book_tag_alt_tag FOREIGN KEY (tag_id) REFERENCES tag (id),
	CONSTRAINT book_tag_alt_book FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE
);

CREATE TABLE note (
	book_id int NOT NULL,
	note varchar(100),
	CONSTRAINT note_book FOREIGN KEY (book_id) REFERENCES book (id)
);
