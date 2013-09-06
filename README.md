ssql
====

[![Build Status](https://travis-ci.org/amkt922/ssql.png)](https://travis-ci.org/amkt922/ssql)

The SSql is a simple database access library.  
It has two main features:  

* build a sql with methods and execute it(Simple Query)
* execute a sql written in a sql-file outside an app(Simple Sql).

The second is inspired by the dbflute outside sql.  
[dbflute](http://dbflute.seasar.org/)  
[About OutsideSql](http://dbflute.seasar.org/ja/manual/function/ormapper/outsidesql/index.html)
  
SSqlはシンプルなデータベースアクセスライブラリです。
主に2つの機能があります。
* メソッドチェインによるSQLの構築と実行
* sqlファイルに記述されたSQLの実行

2つ目の機能はdbfluteの外出しSQLに感銘を受けて作りました。  
[dbflute](http://dbflute.seasar.org/)  
[dbfluteの外出しSQL](http://dbflute.seasar.org/ja/manual/function/ormapper/outsidesql/index.html)
  
# Motive
In the actual project, sometimes we need to access databases with more complex sqls than functions a ORM library has.   
When executing them with a ORM, sources are going to be more complicated generally.  
I need to embed them in the sources, and I don't know whether they are corrent grammertically.   
*The dbflute* has solved such matters with a function that is called *outside sql*, however the dbflute is implemented in Java.  
I have wanted to such a library in PHP, and then implemented a library that has outside sql feature.

# 動機
実際の業務では、ORMが持っている機能以上のDBへのアクセス処理を実装する必要が時々あります。  
ORMの機能でそのようなSQLを発行する場合、大抵は実際にWebアプリケーションで確認するまで文法的に正しいかはわかりません。   
dbfluteはそのような問題を外出しSQLで解決しています。ただdbfluteはJavaで実装されており、PHPでそのようなライブラリが欲しかったため、PHPで外出しSQLを実装したライブラリです。  

# What is the outside sql?
The *outside sql* is a function that execute a sql that is written in sql file.   
You write a sql with comment that is called *a parameter comment*.

# 外出しSQLって？
外出しSQLとはテキストファイルに記載されたSQLを実行する機能です。  
パラメータコメントと呼ばれるコメントとともにSQLを記載します。

# What is the Parameter comment?
Its example is below.  
/\*IF \*/, /\*BEGIN\*/ and so on are parameter comments.

```sql
/*IF paging*/
SELECT
     id
     , name
     , status
     , created_at
-- ELSE SELECT count(id)
/*END*/
FROM
    user
/*BEGIN*/
WHERE
    /*IF name != null*/
    name like /*name*/'%to'
    /*END*/
    /*IF status != null*/
    AND status = /*statu*/1
    /*END*/
/*IF paging*/
ORDER BY id asc
/*END*/
```

# パラメータコメントって？
機能はdbfluteを模倣しているので、dbfluteの外付けSQLのページでご確認ください。  
[About OutsideSql](http://dbflute.seasar.org/ja/manual/function/ormapper/outsidesql/index.html)

# Differences from dbflute as of now.
* dbflute's embedded parameter is written with $, but SSql uses @ 

# 現時点でのdbfluteとの相違点
* dbfluteの埋め込み変数は$を使いますが、SSqlでは@を使います

# How to install the SSql.
```php
require_once SSql.php;
use SSql\SSql;
```
just import SSql.php

# SSqlのインストール方法
```php
require_once SSql.php;
use SSql\SSql;
```
SSql.phpを読み込むだけです。

# Requirements, 環境
* php >= 5.3

# Limitation, 制限
Supports Sqlite, Mysql and Postgresql, other databases are not support as of now.

MysqlとSqlite, Postgresqlのみサポートしています。他のデータベースは現時点ではサポートしていません。

# Usage
Note:
These example are parts of SSql features.  
There are many another functions in the SSql, please check test code.  
Of course I will set up documents in the future.

## Setup
First of all, set up $config like this,  

```php
$config = array('database' => array('driver' => 'Sqlite' <- or Mysql
									, 'dsn' => 'sqlite:./db/db.sqlite3'
									, 'user' => ''
									, 'password' => '')
				'sqlDir' => './sql');
```

## Simple Query
When you want to execute a simple sql, you can use SQueryManager(Simple Query).  
\* When you don't need to execute complex sql.

```php
	$ssql = SSql::connect($config);
	$users = $ssql->createSQry()
					->select(array('id', 'name'))
					->from('User')
					->where(array('name like' => 'sato'))
					->execute();
```
1. connect with the config and get a SSql object
2. let it know you use SQueryManager with _createSQry_ method
3. build a sql with some methods, they're _Doctrine_ like
4. execute it, and get a Result

Update, Delete, Insert operations are almost same as above.

```php
	$ssql = SSql::connect($this->config);
	$users = $ssql->createSQry()
					->delete()
					->from('User')
					->where(array('name like' => 'sato'))
					->execute();
	$ssql->createSQry()
					->update('User')
					->set(array('name' => 'kato'))
					->where(array('id =' => 1))
					->execute();
	$ssql->createSQry()
					->insert()
					->into('User', array('id', 'name'))
					->values(array(array(6, 'tanaka')))
					->execute();
```
They build sqls like below.

```sql
DELETE FROM User WHERE name like ?;
UPDATE User SET name = ? WHERE id = ?;
INSERT INTO User (id, name) VALUES (?, ?);
```

## Simple Sql
When you want to execute a complicate sql, you can use SSqlManager(Simple Sql).  

```php
	$ssql = SSql::connect($this->config);
	$users = $ssql->createSSql()
						->selectList('selectUser', array('id' => 1
														, 'status' => 2
														, 'paging' => true));		

```

1. create sql files wherever you want. it's path should be set in $config['sqlDir'].
2. connect with the config and get SSql object(same as Simple Query)
3. execute selectList with sqlfile name(without extension) and parameters for sqlfile

```sql:selectUser.sql
/*IF paging*/
SELECT
     id
     , name
     , status
     , created_at
-- ELSE SELECT count(id)
/*END*/
FROM
    user
/*BEGIN*/
WHERE
    /*IF id != null*/
    id = /*id*/2
    /*END*/
	/*IF status != null*/
	AND status = /*status*/10
	/*END*/
/*IF paging*/
ORDER BY id asc
/*END*/
```
SSql builts a sql below and execute.  
The written parameters in the selectUser.sql, 2 of id and 10 of status, are trimmed.
This advantage of sql file with parameter comment is that you can build and test sql in Database tool(e.g MySqlWorkbench), and then controll parameters in you application with parameter.

```sql:SelectUser.sql
SELECT
     id
     , name
     , status
     , created_at
FROM
    user
WHERE
    id = 1
	AND status = 2
ORDER BY id asc
```

If you don't pass id, AND is removed automatically.

```sql:SelectUser.sql
SELECT
     id
     , name
     , status
     , created_at
FROM
    user
WHERE
	status = 2
ORDER BY id asc
```
If parameter, *paging* is false, ELSE line is valid and ORDER is removed.

```sql
SELECT count(id)
FROM
    user
WHERE
    id = 1
	AND status = 2
```

## Othres
The SSql has beginTransaction, commit, rollback methods itself.

```php
	$ssql = SSql::connect($this->config);
	$ssql->beginTransaction();
	
	~~~~~~update data~~~~~~~~

	if ($success) {
		$ssql->commit();
	} else {
		$ssql->rollback();
	}
```
