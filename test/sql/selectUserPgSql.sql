select 
	id
	, name
	from /*@@schema*/.user
	/*BEGIN*/
	where 
		/*IF id != null*/
		id = /*id*/1
		/*END*/
	/*END*/