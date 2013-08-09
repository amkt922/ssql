/*IF paging*/
select 
	id
	, name
-- ELSE select count(id)
/*END*/
	from user
/*IF paging*/
order by id /*@idOrder*/desc
/*END*/