create table manager_user_role (
    id int(11) not null auto_increment,
    username varchar(64) not null default '',
    primary key pk_id(id),
    unique key uk_username(username)
) Engine=Innodb default charset utf8;

权限实现的设计思想：
1. 采用access_control对权限使用范围进行约束
2. 用户在奇虎登陆后，默认给他ROLE_USER的权限，该ROLE_USER将作为access_control的基本角色，并保证其他的一些security vote不会做受限处理
3. 利用新的auth voter，对权限再重新进行加载处理，并对access_decision_manager设置unanimous，使得新的voter能够起作用
4. 利用新的auth voter，完成对授权的处理和验证