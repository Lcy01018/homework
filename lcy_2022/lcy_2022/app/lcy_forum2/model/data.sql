-- 创建数据库
CREATE DATABASE lcy_forum
    DEFAULT charset utf8
    COLLATE utf8_general_ci;
use lcy_forum;
-- 创建用户表
create table lcy_user 
(
    unick VARCHAR(10) PRIMARY KEY,
    upa char(32), 
    uemail VARCHAR(30),
    utel VARCHAR(15),
    uimg char(46) default 'me.png'

);


-- 创建测试用户
insert into lcy_user(unick,upa)
    VALUES('tom',md5(123));

-- 创建用户
create USER 'lcy'@localhost IDENTIFIED by '87654321';

-- 授权
GRANT SELECT,UPDATE,insert on lcy_forum.* to 'lcy'@localhost;

-- 由于在MySQL 8.0.11中，caching_sha2_password是默认的身份验证插件，而不是以往的mysql_native_password
use mysql;
ALTER USER 'lcy'@localhost IDENTIFIED WITH mysql_native_password BY '87654321';
FLUSH PRIVILEGES;
use lcy_forum;
-- 创建原贴表
create table lcy_mes
(
    mid int AUTO_INCREMENT PRIMARY KEY NOT NULL,
    mtitle VARCHAR(30) NOT NULL,
    mcontent text NOT NULL,
    munick VARCHAR(10) NOT NULL,
    mcreateat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    msid int NOT NULL

);
ALTER table lcy_mes MODIFY  mcreateat TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

INSERT INTO lcy_mes ( mtitle,mcontent,munick,msid ) VALUES(
     '新帖子01','新帖子内容01','lcy',1
);

INSERT INTO lcy_mes ( mtitle,mcontent,munick,msid ) VALUES(
     '新帖子02','新帖子内容02','lcy',1
);
INSERT INTO lcy_mes ( mtitle,mcontent,munick,msid ) VALUES(
     '新帖子03','新帖子内容03','lcy',1
);

-- 创建回复表
CREATE TABLE lcy_res 
(
    rid int AUTO_INCREMENT PRIMARY KEY NOT NULL,
    rcontent text,
    runick VARCHAR(10) NOT NULL,
    rcreateat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rmid int NOT NULL
);
INSERT INTO lcy_res (rid,rcontent,runick,rmid  ) VALUES(
    1,'新帖子01的回复','tom',1
);
-- 创建板块表
CREATE TABLE lcy_section
(
    sid int AUTO_INCREMENT PRIMARY KEY NOT NULL,
    sname VARCHAR(20) NOT NULL,
    sremark VARCHAR(50) NOT NULL
);

INSERT INTO  lcy_section ( sname,sremark ) VALUES
('求助交流','求助交流'),
('ThinkPHP专区','THinkphp专区'),
('技术合作','技术合作');