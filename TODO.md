Continue, 
1. When starting timer from task index.
Illuminate\Database\QueryException
vendor/laravel/framework/src/Illuminate/Database/Connection.php:857
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'timerable_type' in 'where clause' (Connection: mysql, Host: som-db, Port: 3306, Database: som, SQL: select * from `timers` where `user_id` = 2 and `timerable_type` = App\Models\Task and `timerable_id` = 16 and `completed_at` is null limit 1)
2. Timer sync using is not handled in the device.

SELF NOTE: 
1. Record & Track Leads
2. Journal the activities with audio recording.
3. Feature to store Voice call recording attachments or In-App Voice calls feature. 
4. Allow a way to store crazy ideas to build 