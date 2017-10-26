<?php
一.什么是Redis?
它是Remote Dictionary Server (远程数据服务)的缩写
由意大利人 antirez 开发的一款内存高速缓存数据库，该软件由c语言编写 它的数据模型为
 key-value ,它支持丰富的数据结构，比如 string list hash set sorted set
 可持久化（随时备份），保证了数据安全。
 
 同一个select查询语句，每天需要被执行查询100万次，为了减轻数据库的负载 ，就把查询好的
 数据缓存起来，每天的第一个用户执行从mysql中获得数据并存储到内存中，第二个到第100万
 个用户就直接从内存中获得数据。
 
使用缓存减轻数据库的负载。
在开发网站的时候如果有一些数据在短时间之内不会发生变化，而他们还要被频繁的访问，
为了提高用户的请求速度和降低网站的负载，就把这些数据放在一个读取速度更快的介质上
该行为就称作对该数据的缓存，该介质可以是文件，内存，内存经常用于数据缓存


缓存的两种形式
1.页面缓存 经常用在CMS(content manage system)内存管理系统里边（smarty缓存）
2.数据缓存 经常用在页面的具体数据里边

二.安装Redis   
1.先安装 redis.2.6.14.tar.gz上传到ftp
创建目录mkdir  ll 查看权限列表          给权限 chmod o+w rdtar
进入解压目录直接make即可
进入 src 里面有2个需要记住的文件 1.redis-cli 客户端  2.redis-server 启动服务
创建redis的运行目录 将上面两个文件拷贝至创建好的目录中
cp  redis-cli redis-server /user/local/redis
将redis.2.6.14.tar.gz下redis-config也拷贝至创建到的目录中
进入运行目录中，里面有以上三个文件
启动服务 前面记得加路劲  ./
端口 6379 默认是前台启动服务   
ctrl+c  关闭前台服务

修改配置文件 
vi redis-config?   17 行 daemonize no 改为yes 

后台开启服务
带着redis-config 
查看进程 ps-A | grep redis
启用客户端 ./redis-cli
set 和 get 分别设置和读取信息


三.具体使用
 redis中数据的模型为:key/value
 类似在php中定义变量: 名称 = 值
 
 1.key的操作
 除了\n和空格，其他的都可以拿来命名
 exists key             测试是否存在
 del key1 key2 ...keyN  删除key
 type key               返回指定key的value类型
 keys pattern           返回匹配指定模式的所有key
 rename oldkey newkey   改名字
 dbsize                 返回当前数据库的 key 的数量
 expire key seconds     为key指定过期时间
 ttl key                返回key的剩余秒数
 select db-index        选择数据库 索引指最多为15，一共有16个数据库
 move key db-index      将key从当前数据库移动到指定数据库
 flushdb				删除当前数据库中的所有key
 flushall               删除所有数据库中的所以key  
 
查看当前数据库中以n开始的key的名称

2.string 类型操作
set key value          设置key对应的值为string类型的value
mset key1 value1...
          keyn valuen  一次性设置多个key值
incr key               对key的值做加加操作,并返回新的值，已有key的值，必须是整形禅寺可以操作
decr key               对key的值做减减操作,并返回新的值
incrby key integer     同incr,加指定的值
decrby key integer     同decr,减指定的值
append key value       给指定 key 的字符串值追加 value,value值最好加上双引号，如果value有空格
substr key start end   返回截取过的 key 的字符串值

3.数据类型List 链表
list类型其实就是一个双向链表，通过push pop 操作从链表的头部或者尾部添加
删除元素，这使得list即可以用作栈，也可以用作队列。
上进上出：栈        上进下出：队列

该list链表类型应用场合：
	获得最新的 n 个用户登录信息，select * from user order by logintime desc limit 10
	以上sql语句可以实现 用户需求，但是数据多的时候，全部数据都要受到影响，对数据库
	的负载比较高，必要情况还需要给关键字段（ID 或 logintime）设置索引，索引页比较耗费系统资源
	
如果通过list链表实现以上功能，可以在List链表中只保留最新的10个数据，每进来一个新
数据就删除一个旧数据，每次就可以从链表中直接获得需要的数据。极大的节省各方面的资源消耗
 
list类型操作
lpush key string      在 key 对应 list 的头部添加字符串信息
rpop key              从list 的尾部删除元素，并返回删除元素
llen key 返回 key      对应list 的长度,key不存在返回0，如果key对应的类型不是list，返回错误
lrange key start end  返回指定区间内的元素,下移从0开始
rpush key string      同上,在尾部添加
lpop key              从list 的尾部删除元素，并返回删除元素
ltrim key start end   截取list,保留指定区间内元素

4.set集合类型
redis的set是string 类型的无序集合
set 元素最大可以包含(2的32次方-1)个元素
关于set集合类型除了基本的添加删除操作,其他有用的操作还包含集合的取并集（union）
交集(interseotion),差集(difference),通过这些操作可以很容易的实现sns中的好友推荐功能
注意:每个集合中的各个元素不能重复

该类型应用场景：qq好友推荐

set类型操作
sadd key member          添加一个string 元素到 key 对应的 set 集合中,成功返回1
					     如果元素已经在集合中 返回0,key对应的set不存在返回错误
srem key member [member] 从 key 对应 set 中移除给定元素,成功返回1
smove p1 p2 member       从 p1 对应 set 中移除 member 并添加到 p2 对应 set 中
scard key                返回 set 的元素个数
sismember key member     返回 member 是否在 set 中
sinter key1 key2 ...keyN 返回所有给定 key 的交集
sunion key1 key2 ...keyN 返回所有给定 key 的并集
sdiff  key1 key2 ...keyN 返回所有给定 key 的差集,(前者对后者求差集,结果只有前者的信息)
sismembers key           返回 key 对应 set 的所有元素,结果是无序的		

5.sort set 排序集合类型
它是 list 和 set 的集中体现,称为排序集合类型,也是 string 类型元素的集合
不同的是每个元素都会关联一个权,通过权值可以有序的获取集合中的元素

该类型应用场景：获取最热门的帖子(回复量)前5个
	  		  
sort set 类型操作
zadd key score member       添加元素到集合,元素在集合中存在则更新对应 score
zrem key member             删除指定元素,1表示成功,如果 元素不存在返回0
zincrby key incr member     按照 incr 幅度增加对应 member 的 score 值, 返回 score 值
zrank key member            返回 指定元素在集合中的排名(下标),集合中元素是按 score 从小到大排序的 
zrevrank key member         同上,但是集合中元素是按 score 从大到小排序的
zrange key start end        类似 lrange 操作从集合中指定区间的元素,返回的是有序结果
zrevrange key start end     同上,返回结果是按 score 逆序的
zcard key                   返回集合中元素个数
zscore key element			返回给定元素对应的 score
zremrangebyrank key min max 删除集合中排名在给定区间的元素(权值从小到大排序)
       
 hash 数据类型存储的数据与mysql数据库中存储的一条记录极为相似
 hset key field value       设置 hash field 为指定值,如果 key 不存在,则先创建
 hget key field             获取指定的 hash field 
 hmget key field1...fieldN  获取全部指定的 hash field
 hmset key field1 value1 ...fieldN valueN
 							同时设置 hash 的多个field
 hincrby key field integer  将指定的 hash field 加上给定值
 hexists key field          测试指定的 field 是否存在
 hdel key field             删除指定的 hash field
 hlen key                   返回指定的  hash 的 field 的数量
 hkeys key                  返回 hash 的所有 field
 hvals key                  返回 hash 的所有 value
 hgetall key                返回 hash 的所有 field 和 value
 
三.持久化功能
redis 为了内部数据的安全考虑,会吧本身的数据以文件形式保存到硬盘一份，在服务器重启之后会自动把硬盘的数据恢复到内存(redis)的里面,
数据保存到硬盘的过程就称为"持久化"效果。
1.snap shotting 快照持久化 (dump.db)
  该持久化默认开启,一次性把radis 中全部的数据保存一份存储在硬盘中,如果数据非常多(10~20G)就不适合频繁进行该持久化操作。
  该备份机制：save 900 1 save 300 10 save 60 10000
  
  1.1 手动发起快照持久化
  ./redis-cli bgsave
2. append only file (AOF 持久化 < 也可以叫 精细持久化>)->'在每个小时的范畴之内都做一个“精细'持久化,精细到每秒制作一个持久化,
	这精细持久化保存的数据就是当前一个小时变化的数据
本质：把用户执行的每个“写”指令(添加,修改,删除)都备份到文件中,还原数据的时候就是执行具体写指令而已.
它默认是不开启的。需要在config文件中第368行把no改为yes ，把旧进程干掉,kill-9,然后根据新的配置文件（./redis-server redis-config）,重启 redis ps -A | grap redis
开启后,会自动清除目前 radis 中的全部数据,./redis-cli bgrewriteaof  为其做优化压缩处理，例如将多个incr指令转化成一个set指令

相关指令 
bgsave       异步保存数据到磁盘
lastsave     返回上次成功保存到磁盘的unix时间戳
shutdown     同步保存到服务器并关闭redis服务器
bgrewriteaof 当日志文件过长时优化AOF日志文件存储
./redis-cli -h 127.0.0.1 -p 6371 bgsave 为别人保存 127.0.0.1换成相应的服务器ip地址

主从模式
为了降低每个 redis 服务器的负载,可以多设置几个,并做主从模式,一个服务器负载"写"(添加,修改,删除)数据,其他服务器负载“读”数据
主服务器数据会"自动"同步给从服务器
具体操作
在config162中，将slaveof 前 的#号去掉，后面紧跟需要连接的IP地址 和 redis端口号6379,杀掉旧进程，启动新进程
此时就可以看到主服务器自动同步给从服务器的数据,从服务器默认只读

五.php与redis的结合
1.安装php的redis扩展
 首先将phpredis压缩包 和 autoconfig两个压缩包上传,解压压缩包 tar zxf +压缩包名称
2.通过php 操作redis
3.顺序:redis 与其他软件(xml,gd,jpeg等等)都是php的扩展(php依赖扩展软件)正确的安装顺序是先安装依赖软件,在安装php软件
	  此时,redis 与 php 的安装顺序有前后颠倒的意味,但是php允许 redis 反方向安装进来,在phpredis 的解压目录下运行/user/local/php/bin/phpize,
	 以便redis反方向安装进php里边,执行phpize时发现提示没有软件依赖,解压autoconfig文件,./configure && make install.
	 继续安装phpize,直到提示安装成功。
	下边开始安装phpredis.
	带着php-config参数值给 phpredis做配置
		./configure --with-php-config-/usr/local/php/bin/php-config
		之后执行make && make install
		在/usr/local/php/lib/php.ini配置文件中开启redis,即在最后一行添加extension=redis.so,之后重启阿帕奇,/usr/local/http2/bin/apachect1/restart
		 检验redis是否在其中
4.通过php操作redis
在php里面,redis就是一个功能类 Redis,Redis 类里边有许多成员方法(名字基本与redis指令的名字一致,参数也一致)
通过php实现对redis的操作
a.实例化Redis对象
$red = new redis();
b.链接Redis服务
$red->connect('localhost','6379');
c.具体操作
$red->select();
$red->set();
方法名基本同和指令差不多，只是，需要注意的是,在涉及到多组数据时，需要用到数组，不管是输出还是获取
如:$red ->mset(array('sub1'=>'php','sub2'=>'html','sub3'=>'java'));
   $red ->mget(array('sub1','sub3'));
   利用反射感知 Redis 类有什么方法可以供操作
  通过Redis类实例化一个反射类对象 $me = new $ReflectionClass('Redis');
通过对象获得Redis类的全部方法 $me->getMethods();
