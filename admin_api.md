"LOGIN"
/api/admin/login POST, returns a json 

input: email:string, password:string
output1: {"result_code": 1000, "data": token} -> SUCCESS
output2: {"result_code": 1102} -> INVALID_PASSWORD
output3: {"result_code": 1113} -> INVALID_EMAIL
-----------------------------------------------------------------------------------------------------------
"CHECK TOKEN"

/api/admin/check_token POST, returns a json 

input: token:string
output1: {"result_code": 1000, "data": token} -> SUCCESS
output2: {"result_code": 1103} -> INVALID_TOKEN
-----------------------------------------------------------------------------------------------------------
"GET POST LIST"

/api/admin/posts/{type}/{chunk_count}/{page_count}/{search_phrase}/{group_id} POST, returns a json 
parameters: 
	type -> 'html', 'media', 'program'
	search_phrase, group_id -> set null if you dont want
	chunk_count -> how many result you want per each request
	page_count -> which chunk you want

input: token:string
output1: {"result_code": 1104} -> NO_MORE_POSTS
output2: {"result_code": 1103} -> INVALID_TOKEN
output3: {"result_code": 1000, data: json_array(type_json_object)} -> SUCCESS
	type_json_object:
		type: html -> {id:int, title:string, preview_content:string, content:string, user_id:int, created_at:string(2018-07-28 02:13:09), updated_at:string}
		type: program -> {id:int, title:string, preview_content:string, content:string, group_id:int, user_id:int, created_at:string(2018-07-28 02:13:09), updated_at:string}
		type: media ->{id:int, title:string, description:string, media:string, user_id:int, created_at:string(2018-07-28 02:13:09), updated_at:string}
-----------------------------------------------------------------------------------------------------------
"GET GROUP LIST"

/api/admin/groups POST, returns a json 
input: token:string
output1: {"result_code": 1103} -> INVALID_TOKEN
output2: {"result_code": 1000, data: json_array(json_object)} -> SUCCESS
	json_object:
		{id:int, title:string, created_at:string(2018-07-28 02:13:09), updated_at:string}
-----------------------------------------------------------------------------------------------------------
"CREATE POST"

/api/admin/post/create POST, returns a json 
input: token:string, title:string, preview_content:string, content:string
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1103} -> INVALID_TOKEN
validations: title, preview_content -> max:255|required
			 content -> required
-----------------------------------------------------------------------------------------------------------
"UPDATE POST"

/api/admin/post/update/{id} POST, returns a json 
input: token:string, title:string, preview_content:string, content:string
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
validations: title, preview_content -> max:255|required
			 content -> required
-----------------------------------------------------------------------------------------------------------
"DELETE POST"

/api/admin/post/delete/{id} POST, returns a json 
input: token:string
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
-----------------------------------------------------------------------------------------------------------
"CREATE PROGRAM"

/api/admin/program/create POST, returns a json 
input: token:string, title:string, preview_content:string, content:string, group_id:int
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1103} -> INVALID_TOKEN
validations: title, preview_content -> max:255|required
			 content, group_id -> required
-----------------------------------------------------------------------------------------------------------
"UPDATE PROGRAM"

/api/admin/program/update/{id} POST, returns a json 
input: token:string, title:string, preview_content:string, content:string, group_id:int
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
validations: title, preview_content -> max:255|required
			 content, group_id -> required
-----------------------------------------------------------------------------------------------------------
"DELETE PROGRAM"

/api/admin/program/delete/{id} POST, returns a json 
input: token:string
output1: {"result_code": 1000} -> SUCCESS********
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
-----------------------------------------------------------------------------------------------------------
"CREATE MEDIA"

/api/admin/media/create POST, returns a json 
input: token:string, title:string, description:string, media:file
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1103} -> INVALID_TOKEN
output3: {"result_code": 1114} -> INVALID_FILE
validations: title, description -> max:255|required 
			 media -> max:10mb|extension:mp3|required
-----------------------------------------------------------------------------------------------------------
"UPDATE MEDIA"

/api/admin/media/update/{id} POST, returns a json 
input: token:string, title:string, description:string, media(optional):file
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
output3: {"result_code": 1114} -> INVALID_FILE
validations: title, description -> max:255|required 
			 media -> max:10mb|extension:mp3
-----------------------------------------------------------------------------------------------------------
"DELETE MEDIA"

/api/admin/media/delete/{id} POST, returns a json 
input: token:string
output1: {"result_code": 1000} -> SUCCESS
output2: {"result_code": 1105} -> POST_NOT_EXIST
output3: {"result_code": 1103} -> INVALID_TOKEN
-----------------------------------------------------------------------------------------------------------
