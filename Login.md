#explanation of how login in apedog works
# Introduction #

Explanation of how login in apedog works


# Details #

**1. Succesful use case:** User wants to access apedog.

2.1 If last user session is still alive it will redirect him to main\_page.php.

2.2 If there is no session set, user gets to index.php which is page with neutral template and possibility of login. User inserts username and password, if those match with one of $USERS array defined in index.php session is set and user is redirected to the main\_page.php.

**3.** After user finishes work, he logs out, the session is terminated and user is redirected to the index.php

**Checking of login:** Everytime user wants to access some inner page of a system there is a check if session with user is set, if not, user is redirected to index.php

**Changing pictures:** Based on the inserted username, picture in the top menu is chosen.

**'Useful links'** are accessible either logged in or logged out - top menu of the page is depending on that.