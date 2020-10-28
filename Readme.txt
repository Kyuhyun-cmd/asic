# How to excute

nodejs cmxreceiver_asic.js
-This file includes below, so you need to modify them.

-Authentication & Listening
  .Port : Which port uses to get scanning API data
  .Secret : Scanning API Authentication
  .Validator : API Authentication

-SQL Information
  .host : your DB Server IP
  .user & password : For access to your DB Server
  .Database & DB Table : To insert scanning API Data

-DB Table  : You need to create table like below.

MariaDB [cmx_info]> show full columns from cmx_info;
+------------+-----------+--------------------+------+-----+---------+----------------+---------------------------------+---------+
| Field      | Type      | Collation          | Null | Key | Default | Extra          | Privileges                      | Comment |
+------------+-----------+--------------------+------+-----+---------+----------------+---------------------------------+---------+
| no         | int(11)   | NULL               | NO   | PRI | NULL    | auto_increment | select,insert,update,references |         |
| IPV4       | char(15)  | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| AP_MAC     | char(17)  | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| CLIENT_MAC | char(255) | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| SEEN_TIME  | char(255) | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| MANU       | char(255) | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| OS         | char(255) | utf8mb4_general_ci | NO   |     | NULL    |                | select,insert,update,references |         |
| LAT        | double    | NULL               | YES  |     | NULL    |                | select,insert,update,references |         |
| LNG        | double    | NULL               | YES  |     | NULL    |                | select,insert,update,references |         |
| X          | double    | NULL               | YES  |     | NULL    |                | select,insert,update,references |         |
| Y          | double    | NULL               | YES  |     | NULL    |                | select,insert,update,references |         |
+------------+-----------+--------------------+------+-----+---------+----------------+---------------------------------+---------+