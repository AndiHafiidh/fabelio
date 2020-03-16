###################
Fabelio test (fullstack developer)
###################

This is my result for fullstack developer at fabelio.com

*******************
Demo
*******************

For the result you can visit at `Demo page
<https://fabelio-test-andi.herokuapp.com/>`_ page.

**************************
How to migrate the database
**************************
Migration file can be read at ./application/migrations
https://api-fabelio-test-andi.herokuapp.com/api/make/migrate



*******************
Updating product data
*******************
For updating product data you must create a cron job that's run every minutes using command like this:
\* * * * * /usr/bin/wget --spider "https://fabelio-andi.herokuapp.com/api/product/update" >/dev/null 2>&1