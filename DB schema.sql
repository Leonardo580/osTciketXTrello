create table repos (
        id int(10) unsigned AUTO_INCREMENT,
        title varchar(255),
        description text,
        creator int(11) unsigned,
        dateCreated date default sysdate(),
        constraint primary key (id),
        constraint fk_rp foreign key (creator) references ost_staff(staff_id) on delete cascade on update cascade
        );
create table boards (
                        id int(10) unsigned auto_increment primary key,
                        id_repo int(10) unsigned,
                        title varchar(100),
                        constraint fk_bb foreign key (id_repo) references repos(id) on delete cascade on update cascade
);
create table members (
    id_repo int(10) unsigned,
    id_user int(11) unsigned,
    constraint fk_m foreign key (id_repo) references repos(id) on delete cascade on update cascade ,
    constraint fk_u foreign key (id_user) references ost_staff(staff_id) on delete cascade on delete cascade,
    constraint pk_m primary key (id_repo, id_user)
);

create table Cards(
    id int(10) unsigned AUTO_INCREMENT,
    id_board int(10) unsigned,
    title varchar(100),
    description text,
    constraint pk_c primary key (id),
    constraint fk_c foreign key (id_board) references boards(id) on delete cascade on update cascade
);

create table activities(
    id int(10) unsigned AUTO_INCREMENT,
    id_card int(10) unsigned,
    id_user int(11) unsigned,
    content varchar(128),
    status int(8) unsigned,
    assignedTo int(11) unsigned default id_user,
    expected date,
    assignTo int(11) unsigned default id_user,
    id_ticket int(11) unsigned default null,
    constraint pk_ac primary key (id),
    constraint fk_ac foreign key (id_card) references Cards(id) on delete cascade on update cascade ,
    constraint fk_au foreign key (id_user) references members(id_user) on delete cascade on update cascade,
    constraint fk_tc foreign key (id_ticket) references ost_ticket__cdata(ticket_id)
);

create table pending_members (
    token char(40) not null,
    id_user int(11) unsigned,
    id_repo int(10) unsigned,
    tmstmp integer unsigned not null,
    constraint pk_pm primary key (token),
    constraint fk_pm foreign key (id_repo) references repos(id)
);


create table pending_tickets (
    ticket_id int(11) unsigned primary key,
    isActivity bool,
    constraint fk_pt foreign key (ticket_id) references ost_ticket__cdata(ticket_id)
);


create trigger pending_ticket_insert after insert on ost_ticket__cdata
    for each row
    insert into pending_tickets (ticket_id, isActivity) values (NEW.ticket_id, false);

select repos.id into idr from repos inner join boards
                                               on repos.id =boards.id_repo
                                    inner join cards on boards.id = cards.id_board
                                    inner join activities on activities.id_card=cards.id;

drop procedure if exists osticket.updateStatus;

delimiter  $$
create procedure updateStatus()
begin
    update activities set status=IF(
                    status!=2 and date(expected)<date(sysdate())    , 3, status);
end$$