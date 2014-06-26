DevAAC by developers.pl for [TFS 1.0](https://github.com/otland/forgottenserver)
=====
Changes against [schema.sql](https://github.com/otland/forgottenserver/blob/master/schema.sql):

* Create ```towns``` table that is synchronized with towns from the map on each TFS start.
```town_id``` in ```players``` and ```houses``` will not have foreign keys or if they will, will not CASCADE DELETE.

* Add foreign key in table ```houses``` for ```owner``` to ```players```. It makes sense to require that the owner is a valid player in database.
Do not CASCADE DELETE.

* Add foreign key in table ```houses``` for ```highest_bidder``` to ```players```. It makes sense to require that the highest bidder is a valid player in database.
Do not CASCADE DELETE.

* Rename ```creationdata``` column to ```created_at``` in ```guilds``` table.

* Change collation from default/unspecified to utf8_unicode_ci/latin2. Not useful for Tibia Client but OTClient supports ISO/latin2.

* Put bug reports into database.
