var mysql = require('mysql');
var pool = mysql.createPool({
  connectionLimit : 10,
  host            : 'classmysql.engr.oregonstate.edu',
  user            : 'cs340_johnand4',
  password        : '4177',
  database        : 'cs340_johnand4'
});
module.exports.pool = pool;
