ssql
====
SSql is execute a sql simply.
It is inspired by the dbflute outside sql.

SSqlはシンプルにSQLを発行できるライブラリです。
dbfluteの外出しSQLに感銘を受けて作りました。

## Motivation
In the real business scene, sometimes we need read/write data from/to a database with more complex sqls than functions ORM has.
When executing a sql with ORM, generally we don't know whether the sql is grammertically corrent or not
until executing in web application.
The dbflute has solved such matter with outside sql, however the dbflute is implemented in Java.
SSql is reimplemented a outside sql library in PHP.

## 動機
実際の業務では、ORMが持っている機能以上のDBへの読み書き処理を実装する必要が時々あります。  
ORMの機能でSQLを発行する場合、大抵それらのSQLは実際にWebアプリケーションで実行されるまで文法的に正しいかはわかりません。  
dbfluteはそのような問題を外出しSQLで解決しています。ただdbfluteはJavaで実装されており、SSqlは外出しSQL機能をphpで再実装したライブラリです。

## What is the outside sql?
The outside sql is a function that execute a sql is written outside webapplication(text file). 

## 外出しSQLって？
外出しSQLとはWebアプリケーションの外側(外部テキストファイル)に記載されたSQLを実行する機能です。

## What is the Parameter comment?

## パラメータコメントって？

## Differences from dbflute as of now.

## 現時点でのdbfluteとの相違点

## How to install the SSql.

## SSqlのインストール方法
