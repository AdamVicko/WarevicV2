-- C:\Users\Adam\xampp\mysql\bin>
-- mysql -uroot --default_character_set=utf8 < C:\Users\Adam\Desktop\Repositories\WarevicV2\WarevicV2.sql
drop database if exists WarevicV2;
create database WarevicV2 default charset utf8mb4;
use WarevicV2;

--only for cpanel
--alter database databasename charset utf8mb4;

create table person (
    id int not null primary key auto_increment,
    nameAndSurname varchar(255) not null,
    phone varchar (255) not null
);

create table worker (
    id int not null primary key auto_increment,
    person int,
    email varchar(255) not null,
    password char(60) not null,
    role varchar(255) not null
);

create table patient (
    id int not null primary key auto_increment,
    person int,
    birthDate date,
    address varchar(255),
    oib char(11),
    patientComment text
);

create table oxygenConcentrator(
    id int not null primary key auto_increment,
    serialNumber varchar(255) not null,
    workingHour decimal(18,2),
    manufacturer varchar(255),
    model varchar(255),
    oxygenConcentratorComment text,
    buyingDate date
);

create table delivery(
    id int not null primary key auto_increment,
    oxygenConcentrator int,
    patient int,
    worker int,
    deliveryDate date,
    active boolean,
    collection int
);

create table collection(
    id int not null primary key auto_increment,
    worker int, /*another worker can do collection*/
    collectionDate date,
    delivery int
);

alter table patient add foreign key (person) references person (id);
alter table worker add foreign key (person) references person (id);

alter table delivery add foreign key (oxygenConcentrator) references oxygenConcentrator (id);
alter table delivery add foreign key (patient) references patient (id);
alter table delivery add foreign key (worker) references worker (id);
alter table delivery add foreign key (collection) references collection (id);

alter table collection add foreign key (delivery) references delivery (id);
alter table collection add foreign key (worker) references worker (id);

insert into person(nameAndSurname,phone)
values('Admin Operater', '098/1234567');

insert into worker(person,email,password,role)
values('1','naser@gmail.com','$2a$12$Zoy8B0nG.8cDiCanaCpVwew3zNDvZMVSBftGVoSdo1fjFxctW0lFS
','administrator');