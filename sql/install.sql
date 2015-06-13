drop table if exists cp_lcms_author;
create table cp_lcms_author (
    `account_id` int(11) unsigned primary key,
    `access` int(2) not null,

    foreign key (account_id) references login(account_id)
) ENGINE=MyISAM;

drop table if exists cp_lcms_module;
create table cp_lcms_module (
    `id` int(11) unsigned auto_increment primary key,
    `account_id` int(11) unsigned not null,
    `access` int(2) not null,
    `name` varchar(255) not null,

    foreign key (account_id) references cp_lcms_author(account_id)
) ENGINE=MyISAM;

drop table if exists cp_lcms_page;
create table cp_lcms_page (
    `id` int(11) unsigned auto_increment primary key,
    `module_id` int(11) unsigned not null,
    `account_id` int(11) unsigned not null,
    `status` int(2) not null,
    `access` int(2) not null,
    `date` timestamp not null,
    `name` varchar(255) not null,
    `content` text not null,

    foreign key (module_id) references cp_lcms_module(id),
    foreign key (account_id) references cp_lcms_author(account_id)
) ENGINE=MyISAM;