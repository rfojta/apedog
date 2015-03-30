# Introduction #

This time we use pure php hardcoded inside of Apedog class and database to get which countries are able to select.

For each country there is separate database and list of countries is stored inside of apedog\_base database.


# Details #

There should be some yaml file (or xml, but xml is more space consuming and more difficult to read or write).

And then there will be simple tree with needed data as
  * apedog version
    * production
    * development
    * testing
  * db login
  * password
  * main database
  * developer database
  * etc.
  * features
    * enable or disable each feature