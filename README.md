ssql
====
SSql is execute a sql simply.
It is inspired by the dbflute outside sql.

SSqlはシンプルにSQLを発行できるライブラリです。
dbfluteの外出しSQLに感銘を受けて作りました。

#Motivation
In the real business scene and business applications, sometimes we need access database much more complex sql than orm functions.
When executing a sql with ORM, generally we don't know whether the sql is grammertically corrent or not
until executing in web application.

#動機
実際の業務では、時々ORMが持っている標準機能以上のDBへのアクセス処理を実装する必要があります。
ORMの機能でSQLを発行する場合、大抵それらのSQLは実際にWebアプリケーションで実行されるまで文法的に正しいかはわかりません。

The dbflute has solved such matter with outside sql, however the dbflute is implemented in Java.
SSql is reimplemented in PHP.

dbfluteはそのような問題を外出しSQLで解決しています。ただdbfluteはJavaで実装されており、SSqlをphpで再実装しました。


