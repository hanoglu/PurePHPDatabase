# PurePHPDatabase
## About
PurePHPDatabase is a very minimal database written to use on the limited accessed server. It is ONLY PHP database!<br>
PurePHPDatabase does not use any other binary or script file other than PHP! It designed to communicate with using limited accessed server like the servers in univerities which you only can run PHP to make your own university webpage. It coordinates simultaneous user inputs by putting clients in order using file system of local host. By nature it takes many time compared the other database servers but using file system or sparated progress is only way to handle simultaneous clients in PHP(as far as I know).
## How to Use
### Server Side

```
git clone https://github.com/yusufhanoglu/PurePHPDatabase.git
cd PurePHPDatabase
php -S localhost:8000 -t .
```
db.php file can be downloaded and PHP can be started as shown above.

### Client Side
After initializing server side anyone knows password can connect database server by simply using this URL.
```
localhost:8000/db.php?db=DB_NAME&passwd=YOUR_PASSWORD&method=DB_FUNCTION&p1=PARAM1&p2=PARAM2&p3=PARAM3
```
#### Database Functions - DB_FUNCTION Params
```
nameExists : Checks if name 'p1' exists, if exists returns index of name 'p1', if not returns '-1'.
getDataCount : Returns how many datas exists in specific name 'p1', returns 'null' if name 'p1' not exists.
getData : Returns the value of name 'p1's 'p2'th data, returns 'null' if at least one of 'p1' or 'p2' is not defined.
deleteData : Deletes name 'p1's 'p2'th data, if succeed returns '1', if not returns '0'.
deleteName : Deletes name 'p1', if succeed returns '1', if not returns '0'.
addName : Creates name 'p1' in specified DB, if succeed returns '1', if not succeed or name is already exists returns '0'.
addData : Adds a new data 'p2' to name 'p1', if succeed returns '1', if not returns '0'.
setData : Changes the value of 'p2'th data of name 'p1' to new value 'p3', if succeed returns '1', if not returns '0'.
setName : Renames name 'p1' to new name 'p2', if succeed returns '1', if not retruns '0'.
getAllDatabase : Prints all names and corresponding datas of DB.

```
All database functions can be shown by;
```
localhost:8000/db.php
```
#### Logic of The Database
There are names under the DB tree and datas under the names. Word 'name' corresponds the 'key' and 'data's corresponds the 'value's of this 'key'. More than one data can be identified for each name.

## Important: Controlling the Margins
In this project UTF-8 string trim made by hard coded margins. Related to PHP version or language settings(IDK) margins can change. User should set control this margins by connecting this local host URLs;
```
localhost:8000/db.php?db=DB_NAME&passwd=YOUR_PASSWORD&method=addName&p1=CONTROLNAME
localhost:8000/db.php?db=DB_NAME&passwd=YOUR_PASSWORD&method=addName&p1=CONTROLNAME

localhost:8000/db.php?db=DB_NAME&passwd=YOUR_PASSWORD&method=getAllDatabase
```
If output seems like;
```
#CONTROLNAME#
```
It's OK, if not you should set $MARGIN_LEFT and $MARGIN_RIGHT. Additionally DB_NAME.php file should seem like that;
```
<?php $data="#CONTROLNAME#";?>
```

## Safety Notes
This project licensed under GPL v2 and does not offer any safety guarantee.
