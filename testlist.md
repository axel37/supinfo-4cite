# Unit Tests

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
Can read room

## Room

~~Booking fails when room has overlapping bookings~~
~~Booking can start / end on same day as another~~
Test for aliasing bugs (= defensive copies of dates)
~~Has name
Invalid name~~
~~Update~~
~~update invalid name~~
book(): BookingInterface

## Hotel

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
~~Update
update invalid name
update invalid location~~
~~room already in hotel~~
~~Create hotel without rooms~~
createRoom(): Room

## Picture

~~Empty path~~
~~Can read path~~

## User

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

# Entity/Doctrine Testing

## Hotel
~~Create hotel~~
~~Add / get / remove rooms~~
~~Room cascade on deletion~~

## Room

~~Can create a room, and retrieve it from db~~
~~Can get bookings~~
~~Test cascade operations~~

# API Tests

## Hotel

~~Create hotel
Can create hotel without rooms~~
~~Invalid name~~
~~Invalid location~~
~~Add room~~
Create room (Hotel::createRoom)
Remove room
~~Can get hotel after creating it ?~~ 
Description : null is ok, empty string is not

## Room

~~Create room (POST)~~
~~GET room~~
~~PUT is disabled~~
~~PATCH room~~
~~DELETE room~~
View bookings
book
Put room with unknown room id
Invalid booking dates


## Booking
~~Create (POST)~~
~~Create on unavailable slot~~
Create with invalid dates
~~GET booking
GET bookings~~
~~PUT is disabled~~
~~PATCH is disabled~~
~~DELETE booking~~

# User
Can log in
