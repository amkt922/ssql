select 
	id
	, name
	from user
	/*BEGIN*/
	where 
		/*FOR idList*/
		/*NEXT 'or '*/id = /*#current*/1
		/*END*/
	/*END*/