# Introduction #

This manual should guide you step by step through the process of creating sql script on each table to ensure that mysql will know each relation between one table and column and second table and column


# Details #

  1. change the engine of table to innodb
  1. add index for each column, which should be referenc to other table
  1. add foreign key between each reference column and other table id
  1. repeat this for each column

## Exceptions ##

  * Some columns may contain invalid data, this should be detect by simple sql query inquiring values from source column which is not contained in target table ids.