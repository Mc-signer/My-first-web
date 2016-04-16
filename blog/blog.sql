create database blogdb; 
use blogdb;
create table user(
	userId int not null auto_increment,
    name varchar(40) not null,
    password char(40) not null,
    gender char(4),
    age int,
    headImg varchar(50),
    profile tinytext,
    signUpDate date,
    ban int default 0,
    active int default 0,
    primary key(userId)
);
create table article(
	articleId int not null auto_increment,
    title tinytext,
    content text,
    writeTime datetime,
    category varchar(10),
    writeUser int not null,
    primary key(articleId)
);
create table comment(
	commentId int not null auto_increment,
    articleId int not null,
    commentUser varchar(40),
    commentTime datetime,
    commentContent text,
    primary key(commentId)
);
create table reply(
	replyId int not null auto_increment,
    commentId int not null,
    commentUser varchar(40),
    replyUser varchar(40),
    replyTime datetime,
    replyContent text,
    primary key(replyId)
);
create table photo(
	photoId int not null auto_increment,
	filename varchar(64),
    nickname varchar(64),
    addTime datetime,
    ownerId int,
    belongAlbum varchar(20) default 'default',
    primary key(photoId)
);
create table album(
	albumId int not null auto_increment,
    albumName varchar(20),
	albumOwnerId int,
    primary key(albumId)
);
select * from comment;
select * from reply;
select * from user;
select * from article;
alter database blogdb charset utf8;