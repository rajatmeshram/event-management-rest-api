

Api Route and parameters

Create event
http://localhost/wordpress/wp-json/events/v1/create?title=rest event3&description=rest description&start=2023-11-05T11:08&end=2023-11-08T11:08&category=virtual

Update Event

http://localhost/wordpress/wp-json/events/v1/update/?id=2645&title=rest event333&description=rest33 description&start=2023-11-08T11:08&end=2023-11-12T11:08&category=onsite

Single Event
http://localhost/wordpress/wp-json/events/v1/show/?id=2644

Delete Event

http://localhost/wordpress/wp-json/events/v1/delete?id=2634

Display All Events
http://localhost/wordpress/wp-json/events/v1/allevents

Dispaly Event by Date
http://localhost/wordpress/wp-json/events/v1/list?date=2023-11-05T11:08