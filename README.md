__ Requirements and Recommendations: __

1. PHP (recommended version 7.1.7)
2. MySQL (recommended version 5.7.17)
3. Postman (recommended version 5.3.2)
4. Composer (recommended version 1.6.2)

__ Installation Instructions: __

1. Unzip the package for a known folder
2. Create a MySQL database:
  a. Create a MySQL database to hold the data;
  b. Set user and privileges to new database;
  c. Import the schema file name './db/schema.sql' into new database
  d. Open the file './src/settings.php' and access info to database
3. Configure URL to application in file './src/settings.php'
  a. The default port for this application is 80 (standard TCP port, but if unavailable in the machine, some suggestions are 8080, 4200 or 3000)
4. Run 'composer start'
  a. This is a simplest way to run this server. But there is a known bug in php embedded server that may cause problems with this application. More info at: https://stackoverflow.com/questions/29141240/php-local-server-invalid-request-unexpected-eof .
  b. An optional way to run it is an apache web server. Make sure you have php and rewrite modules enabled to do that.
5. Try http://localhost and be happy

__ Testing: __

1. Run 'composer test'

[[[[[[[[[[[[[ ATTENTION!!! ]]]]]]]]]]]]]]
For simplicity of configuration, I was not create a second database for testing purposes. Run the tests will delete the records of ALL tables.
[[[[[[[[[[[[[ ATTENTION!!! ]]]]]]]]]]]]]]

2. I developed tests to API.
3. Testing the APP: is possible to completely test the API, but...
  a. Install solutions like Selenium or Proctactor take a lot of time.
  b. Develop e2e tests either.
  c. I need to know if you need these tests because I am in a rush right now, finish a MVP to someone.

__ About the solution __

1. This is a solution based in Slim micro framework.
2. I am using PDO without any ORM solution. I like ORMs but they are big enough to a small system like this one.
3. Where the files live:
  a. Entry point of system is public/index.php;
  b. API routes lives in src/api;
  c. APP routes are in src/app;
  d. Common libs to APP are in src/libs;
  e. Dependencies uses DI and are instantiated in src/dependencies.php;
  f. Settings are in src/settings.php;
  g. Routes are grouped in src/routes. They are imported fron their respective files;
4. I choose to use bootstrap to put some color, I load this from CDN;
5. The objects have usually very descriptive methods line "Create", "Read","Update","Delete".
  a. Vouchers some even methods like "GenerateCode" and "Use".
6. The most interesting SQL query is a JOIN using 3 tables. It lives in src/api/voucher.php, line 81.
7. I choose to use no javascript in the solution, because the subject of the test is PHP.
  a. I can use javascript to improve the UX, if needed.
8. There is a lot of opportunities to improve this system:
  a. Some are:
    i. More constrains in the database;
    ii. A dynamic interface with animations and some nice validations on client side;
    iii. l18n and i18n;
  b. I believe a developer must pay attention to details.
  c. Despite of item "b", the project must also have balance between costs and delivery quality. I can keep improving, but I need some more directions about what is important to you, guys.
9. You can find the postman exported calls in db/E-Voucher.postman_collection.json .
10. I prefer "implementation over documentation", but I understand the importance of good and detailed documentation in some environments. How detailed you need the documentation?

__ More __

1. Questions? ;)
