# Introduction #

This use case will enable user to add current values and compare it with planned numbers.

# Used Data Tables #

  * TableTracking
  * TableLCs
  * TableUsers
  * TableAreas

# Details #

Sesion contains
  * user login

Page contains
  * SelectTerm component
  * List of areas
  * Input for each area in current term
  * Input contains current value for term and area from TableTracking

# SQL #

For each input

```
Update tracking set actual = 10
where lc = 'Praha'
and Term = '2009/3'
and area = 'ICX';
```


## Related Use cases ##

  * SelectDate
  * [Planning](Planning.md)