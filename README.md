ssql
====
  
SSql is a simple database access library.
It has mainly two features:
* build a sql with methods and execute it.
* execute a sql written in a sql-file outside an app.

Second feature is inspired by the dbflute outside sql.  
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
In the real world, sometimes we need to access databases with more complex sqls than functions ORM has.   
When executing such a sql with ORM, it is going to be more complicated code generally, and we don't know whether the sql is grammertically corrent.   
The dbflute has solved such matter with outside sql, however the dbflute is implemented in Java.  
I have wanted to such a library in PHP, and then implemented a library has outside sql function.

# 動機
実際の業務では、ORMが持っている機能以上のDBへのアクセス処理を実装する必要が時々あります。  
ORMの機能でそのようなSQLを発行する場合、大抵は実際にWebアプリケーションで確認するまで文法的に正しいかはわかりません。   
dbfluteはそのような問題を外出しSQLで解決しています。ただdbfluteはJavaで実装されており、PHPでそのようなライブラリが欲しかったため、PHPで外出しSQLを実装したライブラリです。  

# What is the outside sql?
The outside sql is a function that execute a sql is written in sql file.   
You write a sql with comment called parameter comment.

# 外出しSQLって？
外出しSQLとはテキストファイルに記載されたSQLを実行する機能です。  
パラメータコメントと呼ばれるコメントとともにSQLを記載します。

# What is the Parameter comment?
Its example is below.
It is for controlling sql with IF, BEGIN, etc.

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
* SSql have not implemented one of parameter comments, FOR yet.
* dbflute's embedded parameter is written with $, but use @ in SSql

# 現時点でのdbfluteとの相違点
* パラメータコメントの一つであるFORコメントはまだ実装していません
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

# Usage

Note:
These example are a part of SSql features.  
There are many another features in SSql, please check sources.  
Of course I will set up documents in the future.

when you want to execute a simple sql, you can use SQueryManager(Simple Query).  
Fist of all, set up econfig array like this,  

```php
$config = array('database' => array('dsn' => 'sqlite:./db/db.sqlite3'
									, 'user' => ''
									' 'password' => '')
				'sqlDir' => './sql');
'```

```php
	$ssql = SSql::connect($this->config);
	$users = $ssql->createSQry()
					->select(array('id', 'name'))
					->from('User')
					->where(array('name like' => 'sato'))
					->execute();
```

1. connect with config and get SSql object
2. let ssql object know you use SQueryManager wieh createSQry method
3. build a sql with Doctrine similar method
4. chain methods, execute, and get a Result

Update, Delete, Insert are similar to above.
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

These example build sqls below.

```sql
DELETE FROM User WHERE name like ?;
UPDATE User SET name = ? WHERE id = ?;
INSERT INTO User (id, name) VALUES (?, ?);
```

Next, when you want to execute more complicate sql, you can use SSqlManager(Simple Sql).  
```php
	$ssql = SSql::connect($this->config);
	$users = $ssql->createSSql()
						->selectList('selectUser', array('id' => 1
														, 'status' => 2
														, 'paging' => true));		

```

0. create sql file wherever you want. it place should be set in $config['sqlDir'].
1. connect with config and get SSql object(same as SQryManager)
2. execute selectList with parameter sql filename and parameter for sqlfile

```sql:SelectUser.sql
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

The below sql is going to be built and executed.

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




