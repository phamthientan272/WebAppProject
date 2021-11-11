CREATE TABLE bookings (
id VARCHAR(38) NOT NULL ,
service VARCHAR( 255 ) NOT NULL ,
timeslot VARCHAR( 255 ) NOT NULL ,
date DATE NOT NULL ,
name VARCHAR( 255 ) NOT NULL ,
email VARCHAR( 255 ) NOT NULL ,
phone VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
);

CREATE TABLE service_day_off (
service VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( service ),
dayoff VARCHAR( 255 ) NOT NULL ,
);


CREATE TABLE service_working_hour (
service VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( service ),
working_hour VARCHAR( 255 ) NOT NULL ,
)
