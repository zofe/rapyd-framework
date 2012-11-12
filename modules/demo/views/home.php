Hi, this is Rapyd Demo Module

requirements:
Server Requirements For this demo: PHP >= 5.2


This demo use a mysql database so dump this and check
/application/config.php to connect to right database


<pre class="prettyprint sql-css">
SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `demo_articles` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text,
  `post_date` datetime DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
INSERT INTO `demo_articles` VALUES ('1', '1', 'Title 1', 'Body 1', '2011-05-13 00:00:00', '0'), ('2', '2', 'Title 2', 'Body 2', '0000-00-00 00:00:00', '0'), ('3', '1', 'Title 3', 'Body 3', '0000-00-00 00:00:00', '0'), ('4', '2', 'Title 4', 'Body 4', '0000-00-00 00:00:00', '0'), ('5', '1', 'Title 5', 'Body 5', '0000-00-00 00:00:00', '0'), ('6', '2', 'Title 6', 'Body 6', '0000-00-00 00:00:00', '0'), ('7', '1', 'Title 7', 'Body 7', '0000-00-00 00:00:00', '0'), ('8', '2', 'Title 8', 'Body 8', '0000-00-00 00:00:00', '0'), ('9', '1', 'Title 9', 'Body 9', '0000-00-00 00:00:00', '0'), ('10', '2', 'Title 10', 'Body 10', '0000-00-00 00:00:00', '0');


CREATE TABLE `demo_authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `demo_authors` VALUES ('1', 'Jhon', 'Doe'), ('2', 'Rocco', 'Siffredi');

CREATE TABLE `demo_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `comment` text,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
INSERT INTO `demo_comments` VALUES ('1', '1', 'comment 1'), ('2', '1', 'comment 2'), ('3', '1', 'comment 3');

SET FOREIGN_KEY_CHECKS = 1;
</pre>
<script type="text/javascript">
    $(document).ready(function () {
        prettyPrint();
    });
</script>




