create database labbooking;
use labbooking;

create table faculty (
    faculty_id int primary key auto_increment,
    name varchar(100) not null,
    department varchar(100) not null,
    email varchar(100) not null
);

create table lab (
    lab_id int primary key auto_increment,
    lab_name varchar(100) not null,
    system_count int not null,
    floor varchar(50) not null
);

create table lab_booking (
    booking_id int primary key auto_increment,
    faculty_id int not null,
    lab_id int not null,
    booking_date date not null,
    time_slot varchar(50) not null,
    foreign key (faculty_id) references faculty(faculty_id) on delete cascade,
    foreign key (lab_id) references lab(lab_id) on delete cascade
);

-- sample data

insert into faculty (name, department, email) values
('faculty1', 'Data Science', 'faculty1@mail.edu'),
('faculty2', 'Statistics', 'faculty2@mail.edu'),
('faculty3', 'Computer Applications', 'faculty3@mail.edu'),
('faculty4', 'Mathematics', 'faculty4@mail.edu'),
('faculty5', 'Cybersecurity', 'faculty5@mail.edu');

INSERT INTO lab (lab_name, system_count, floor) VALUES
('AI Lab', 40, '1st Floor'),
('Chavara Lab', 35, '3rd Floor'),
('Carmel Lab', 50, 'Ground Floor'),
('Carmel Lab', 45, 'Ground Floor'),
('CC Lab', 30, '2nd Floor');