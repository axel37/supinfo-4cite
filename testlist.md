# Tests

## Booking

~~Can read dates after creating booking~~
~~New booking should be in the future~~
~~Start must be before end
End must be before start~~
~~Start and end can't be on same day~~
~~Can start booking today~~
Can get start
Can get end
No aliasing (= defensive copies)
Cancel ?

## Room

~~Booking fails when room has overlapping bookings~~
~~Booking can start / end on same day as another~~
Test for aliasing bugs (= defensive copies of dates)
~~Has name
Invalid name~~
Update
update invalid name
room already in hotel

# Hotel

~~See hotel's rooms~~
~~See hotel's bookings~~
Book a room ?
~~Create with valid name and location~~
~~Invalid name~~
~~Invalid location~~
~~See pictures~~
~~Remove picture~~
~~See description
Hotel with no description is ok~~
Update
update invalid name
update invalid location

# Picture

~~Empty path~~
~~Can read path~~

# User

~~make a booking~~
~~Find all user's bookings~~
~~email~~
invalid email format ? (handled by validator ?)
~~username~~
(password)
(role)
~~Update user
update invalid email
update invalid name~~
