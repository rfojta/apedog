# Use Case planning #

The page will enable user to add plans as number according to each area and KPI included.


# Details #

![http://www.websequencediagrams.com/cgi-bin/cdraw?lz=VXNlci0-UGxhbm5pbmc6IEZvcm0gUmVxdWVzdChNeSBMQykKABYILT5UZXJtOiBHZXQgQWN0dWFsIFRlcm0KVGVybS0AOQxUZXJtcyBMaXN0ADELQXJlYXMANwZDdXJyZW50IAAOBQoAFAUAMw0AJgUAMQ8tPlVzZXI6IACBIwgAgSUFCgo&s=napkin.jpg](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=VXNlci0-UGxhbm5pbmc6IEZvcm0gUmVxdWVzdChNeSBMQykKABYILT5UZXJtOiBHZXQgQWN0dWFsIFRlcm0KVGVybS0AOQxUZXJtcyBMaXN0ADELQXJlYXMANwZDdXJyZW50IAAOBQoAFAUAMw0AJgUAMQ8tPlVzZXI6IACBIwgAgSUFCgo&s=napkin.jpg)

## Areas ##

Inputs: Term, LC

Tables: Row for each Area

Input names LC-TERM-AREA

Each of those is number which represents ID into particular table.


## Example ##

Used tables
  * TableTracking
  * TableAreas
  * TableKpis
  * TableTerms

```
Insert into Tracing (
   lc, term, area, target)
values (
   'Praha', '2009/3', 'ICX', 10
);
```

## Related Use cases ##

  * SelectDate

## Related clasess ##

  * ClassPlanning
  * ClassTracking
  * ClassLc
  * ClassTerm