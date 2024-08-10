# REST-API-for-book-library

Task (Backend Developer, PHP, Symfony)

You need to create a REST API for a book directory.
The format of requests/responses is JSON.
1. Data description:

    a) Each book should have:
    1. Title (Required field)
    2. Short description (Optional field)
    3. Image (jpg or png, no larger than 2 MB, must be stored in a separate folder and have a unique file name)
    4. Authors (Required field, a book may have multiple authors)
    5. Publication date of the book

    b) Each author should have:
    1. Last name (Required field, at least 3 characters long)
    2. First name (Required)
    3. Middle name (Optional)

2. API:

    a) Route to create authors;

    b) Route to view the list of all authors;

    c) Route to create books;

    d) Route to view the list of all books;

    e) Route to search books by author's last name;

    f) Route to view a single book;

    g) Route to edit a book;

Note: Pagination should be used when retrieving a list of any entities (authors, books).

General provisions:


Technologies:
Symfony, PostgreSQL
Other technologies/frameworks/libraries can be used at your discretion.

The creation of database tables should be implemented using the migration mechanism.

Anything not specified in the requirements for the test task can be done at your discretion.

1. To start the project, you need to run:

```shell
make up
```

2. To install dependencies, execute:
```shell
$ make composer_install
```

3. Set extended permissions for the var folder in the project's root:
```shell
$ sudo chmod -R 777 ./var
```

4. To apply migrations, execute in the project's root:
```shell
make migration
```

5. After a successful installation, access the documentation at the following link:
    http://127.0.0.1:9580/api/doc
   
6. To run tests, Run once for installation:
```shell
make test_install
```

7. Test using the command:
```shell
make test
```