DROP TABLE IF EXISTS `shop_banner`;
CREATE TABLE IF NOT EXISTS `shop_banner` (
  `bn_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  `bn_device` varchar(10) NOT NULL default 'pc' COMMENT '노출기기',
  `bn_theme` varchar(255) NOT NULL default 'basic' COMMENT '쇼핑몰스킨',
  `bn_code` tinyint(4) NOT NULL default '0' COMMENT '배너코드',
  `bn_file` varchar(255) NOT NULL COMMENT '배너이미지',
  `bn_link` varchar(255) NOT NULL COMMENT '배너URL',
  `bn_target` varchar(10) NOT NULL COMMENT '새창여부',
  `bn_width` int(11) NOT NULL default '0' COMMENT '가로크기',
  `bn_height` int(11) NOT NULL default '0' COMMENT '세로크기',
  `bn_bg` varchar(10) NOT NULL COMMENT '백그라운드색상',
  `bn_text` varchar(255) NOT NULL COMMENT '배너설명',
  `bn_use` tinyint(4) NOT NULL default '0' COMMENT '노출여부',
  `bn_order` int(11) NOT NULL default '0' COMMENT '노출순위',
  PRIMARY KEY  (`bn_id`),
  KEY `mb_id` (`mb_id`),
  KEY `bn_code` (`bn_code`),
  KEY `bn_use` (`bn_use`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='배너관리 테이블' AUTO_INCREMENT=310;

INSERT INTO `shop_banner` (`bn_id`, `mb_id`, `bn_device`, `bn_theme`, `bn_code`, `bn_file`, `bn_link`, `bn_target`, `bn_width`, `bn_height`, `bn_bg`, `bn_text`, `bn_use`, `bn_order`) VALUES
(36, 'admin', 'pc', 'basic', 3, 'Wu8kdNjC5zMm6HRzVtzmZ8tg1CRfb6.jpg', '', '_self', 280, 400, '', '', 1, 0),
(4, 'admin', 'pc', 'basic', 2, '6hTUUw67mkPz6ZJkf74TBdDBk2bqZG.gif', '', '_self', 160, 60, '', '', 1, 0),
(18, 'admin', 'mobile', 'basic', 1, '9Gwm8PfnrTj5KtHuqYnwwJHBTpQkAM.jpg', '', '_self', 960, 120, '', '', 1, 0),
(19, 'admin', 'mobile', 'basic', 2, 'LYdCxA4wbDKpk3vpXYC7ZnZ45WRsZ9.jpg', '', '_self', 475, 270, '', '', 1, 0),
(37, 'admin', 'pc', 'basic', 4, 'AVnBX5paYMDMhc6kr5ndWq7vnEBjHK.jpg', '', '_self', 420, 195, '', '', 1, 0),
(38, 'admin', 'pc', 'basic', 5, 'NGgjGw9KKy1Uc5bXteFsRFYeh16bw4.jpg', '', '_self', 420, 195, '', '', 1, 0),
(39, 'admin', 'pc', 'basic', 6, 'dC5wjsjhj5CyM6RWReRp8NJCm9SsJt.jpg', '', '_self', 1000, 200, '', '', 1, 0),
(40, 'admin', 'pc', 'basic', 7, 'eDgCtJytyjdaAt6s3NpC4ZGE1UZV74.jpg', '', '_self', 1920, 2880, '', '배경은 7번 배너이미지 입니다. <br>여기는 배너 텍스트를 넣어주세요.', 1, 0),
(41, 'admin', 'pc', 'basic', 8, 'jfrGcAfEHjxUSJudVWJSzX2ZuErmJZ.jpg', '', '_self', 480, 290, '', '', 1, 0),
(42, 'admin', 'pc', 'basic', 9, 'Hmgye7tWqqelSug53Fql3c7tMhKQ96.jpg', '', '_self', 200, 290, '', '', 1, 0),
(43, 'admin', 'pc', 'basic', 11, 'zxBj8DMqbQG364XuKMxSaUSx9RLm7M.jpg', '', '_self', 300, 500, '', '', 1, 0),
(44, 'admin', 'pc', 'basic', 10, 'ASChUd9h6Vl5UxlsHhqzETzy7NnRsm.jpg', '', '_self', 690, 200, '', '', 1, 0),
(45, 'admin', 'pc', 'basic', 1, 'szEreKjpPshELPSNnS8esg8RXvRd5T.jpg', '/shop/listtype.php?type=4', '_self', 1000, 70, 'F2877F', '', 1, 0),
(46, 'admin', 'pc', 'basic', 90, 'EPULGkW4hHbVD93V3GqblQpurxErgz.png', '', '_self', 80, 80, 'F4F4F4', '', 1, 0),
(47, 'admin', 'pc', 'basic', 90, 'rkBbmRPAfzbav4j2u2msEszx4LZeXX.png', '', '_self', 80, 80, '', '', 1, 0),
(48, 'admin', 'mobile', 'basic', 3, 'VFavnYbGvhGE4rjwedA5V4p5wYYGkT.jpg', '', '_self', 475, 270, '', '', 1, 0),
(49, 'admin', 'mobile', 'basic', 4, 'dStrtRCPZ3FhRr7MLA7pxdyMrWK5lM.jpg', '', '_self', 960, 233, '', '', 1, 0),
(50, 'admin', 'mobile', 'basic', 5, 'uP2P9k8F4uk5bUctjwb72kJzX8kvtN.jpg', '', '_self', 960, 300, '', '', 1, 0),
(51, 'admin', 'mobile', 'basic', 6, 'HPdwLbj1hguccmtZFXCVQTwfaganyL.jpg', '', '_self', 960, 300, '', '', 1, 0),
(52, 'admin', 'mobile', 'basic', 7, 'fHw1ceKKy3jXusysbVA4gUC3ky1G29.jpg', '', '_self', 960, 300, '', '', 1, 0),
(53, 'admin', 'mobile', 'basic', 8, 'AKepcVeCNBCx7Htd9WfkRWZ5xvkzeK.jpg', '', '_self', 960, 300, '', '', 1, 0),
(141, 'admin', 'pc', 'basic', 0, 'Q9Kb37h7rETJmvCA8R31bkkxrjUq6Z.jpg', '', '_self', 1000, 400, 'eeeeee', '1번 텍스트', 1, 1),
(142, 'admin', 'pc', 'basic', 0, 'c4nVXYmE6PnNnGtz2vHLmaZdXWJtE3.jpg', '', '_self', 1000, 400, 'e7edfa', '2번 텍스트', 1, 2),
(143, 'admin', 'pc', 'basic', 0, 'eCx2X32v8tmnS2drdKQgCAjWYF8nfF.jpg', '', '_self', 1000, 400, 'fee3df', '3번 텍스트', 1, 3),
(144, 'admin', 'mobile', 'basic', 0, '5Zn5J4Mzn3wUelnjKkWVj5FAvqahMG.jpg', '', '_self', 960, 720, '', '', 1, 1),
(145, 'admin', 'mobile', 'basic', 0, 'W9ssZMNNKc3RbASPejwLJG2qHLlpM4.jpg', '', '_self', 960, 720, '', '', 1, 2),
(146, 'admin', 'mobile', 'basic', 0, 'gcrqZeHbZzVmUeufhrfTmr9wHbx5GW.jpg', '', '_self', 960, 720, '', '', 1, 3),
(147, 'admin', 'pc', 'basic', 100, 'UAzBR7GP5uEsq2SqyqgphnlmxAuPvS.gif', '', '_self', 410, 410, '', '', 1, 0),
(148, 'admin', 'pc', 'basic', 100, 'w611LjGVu2GzUdGMkmhuFmlQkkFJW4.gif', '', '_self', 410, 410, '', '', 1, 0);


DROP TABLE IF EXISTS `shop_board_13`;
CREATE TABLE IF NOT EXISTS `shop_board_13` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(11) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(11) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(11) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(11) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 공지사항' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_13_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_13_tail` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(11) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_20`;
CREATE TABLE IF NOT EXISTS `shop_board_20` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(11) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(11) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(11) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(11) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='입점사 공지사항' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_20_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_20_tail` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(11) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_21`;
CREATE TABLE IF NOT EXISTS `shop_board_21` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(11) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(11) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(11) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(11) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='입점사 질문과답변' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_21_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_21_tail` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(11) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_22`;
CREATE TABLE IF NOT EXISTS `shop_board_22` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(11) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(11) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글("Y":비밀글,"N":공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(11) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(11) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 공지사항' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_22_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_22_tail` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(11) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_36`;
CREATE TABLE IF NOT EXISTS `shop_board_36` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(11) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(11) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(11) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(11) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 질문과답변' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_36_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_36_tail` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(11) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(11) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(11) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_41`;
CREATE TABLE IF NOT EXISTS `shop_board_41` (
  `index_no` int(10) unsigned NOT NULL auto_increment COMMENT '주키',
  `btype` int(10) unsigned NOT NULL default '0' COMMENT '형식(1:공지,2:일반)',
  `fid` int(10) unsigned NOT NULL default '0' COMMENT '글등록 순서',
  `ca_name` varchar(100) NOT NULL COMMENT '분류',
  `issecret` char(1) NOT NULL COMMENT '비밀글(Y:비밀글,N:공개)',
  `havehtml` char(1) NOT NULL COMMENT 'HTML 사용여부(미사용)',
  `writer` int(10) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(50) NOT NULL COMMENT '작성자명',
  `subject` varchar(200) NOT NULL COMMENT '글제목',
  `memo` text NOT NULL COMMENT '글내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '파일첨부1',
  `fileurl2` varchar(50) NOT NULL COMMENT '파일첨부2',
  `readcount` int(10) unsigned NOT NULL default '0' COMMENT '조회수',
  `tailcount` int(10) unsigned NOT NULL default '0' COMMENT '댓글수',
  `wdate` int(10) unsigned NOT NULL default '0' COMMENT '등록일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `thread` varchar(255) NOT NULL COMMENT '답글체크',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  `average` char(1) NOT NULL COMMENT '미사용',
  `product` varchar(50) NOT NULL COMMENT '미사용',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  PRIMARY KEY  (`index_no`),
  KEY `btype` (`btype`,`fid`,`wdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 갤러리게시판' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_41_tail`;
CREATE TABLE IF NOT EXISTS `shop_board_41_tail` (
  `index_no` int(10) unsigned NOT NULL auto_increment COMMENT '주키',
  `board_index` int(10) unsigned NOT NULL default '0' COMMENT '게시판주키',
  `writer` int(10) unsigned NOT NULL default '0' COMMENT '회원주키',
  `writer_s` varchar(30) NOT NULL COMMENT '작성자명',
  `memo` text NOT NULL COMMENT '글내용',
  `wdate` int(10) unsigned NOT NULL default '0' COMMENT '작성일',
  `wip` varchar(20) NOT NULL COMMENT '작성자IP',
  `passwd` varchar(20) NOT NULL COMMENT '패스워드',
  PRIMARY KEY  (`index_no`),
  KEY `board_index` (`board_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_board_conf`;
CREATE TABLE IF NOT EXISTS `shop_board_conf` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `gr_id` varchar(100) NOT NULL COMMENT '게시판그룹ID',
  `skin` varchar(50) NOT NULL COMMENT '게시판스킨',
  `list_skin` varchar(50) NOT NULL COMMENT '게시판목록스킨',
  `boardname` varchar(255) NOT NULL COMMENT '게시판명',
  `list_priv` tinyint(4) NOT NULL default '0' COMMENT '목록보기 권한',
  `read_priv` tinyint(4) NOT NULL default '0' COMMENT '글읽기 권한',
  `write_priv` tinyint(4) NOT NULL default '0' COMMENT '글쓰기 권한',
  `reply_priv` tinyint(4) NOT NULL default '0' COMMENT '글답변 권한',
  `tail_priv` tinyint(4) NOT NULL default '0' COMMENT '댓글쓰기 권한',
  `topfile` varchar(255) NOT NULL COMMENT '상단 파일 경로',
  `downfile` varchar(255) NOT NULL COMMENT '하단 파일 경로',
  `use_secret` tinyint(4) NOT NULL default '0' COMMENT '비밀글 사용',
  `use_category` tinyint(4) NOT NULL default '0' COMMENT '분류사용여부',
  `usecate` varchar(255) NOT NULL COMMENT '분류',
  `usefile` char(1) NOT NULL COMMENT '파일업로드 사용여부',
  `usereply` char(1) NOT NULL COMMENT '글답변사용여부',
  `usetail` char(1) NOT NULL COMMENT '댓글쓰기사용여부',
  `width` int(11) NOT NULL default '0' COMMENT '게시판 테이블 폭',
  `page_num` tinyint(4) NOT NULL default '0' COMMENT '페이지당 목록 수',
  `read_list` tinyint(4) NOT NULL default '0' COMMENT '글내용 옵션',
  `list_cut` int(11) NOT NULL default '0' COMMENT '제목 길이',
  `content_head` text NOT NULL COMMENT '상단 내용',
  `content_tail` text NOT NULL COMMENT '하단 내용',
  `insert_content` text NOT NULL COMMENT '글쓰기 기본 내용',
  `fileurl1` varchar(50) NOT NULL COMMENT '상단 이미지',
  `fileurl2` varchar(50) NOT NULL COMMENT '하단 이미지',
  PRIMARY KEY  (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='게시판 환경설정 테이블' AUTO_INCREMENT=42;

INSERT INTO `shop_board_conf` (`index_no`, `gr_id`, `skin`, `list_skin`, `boardname`, `list_priv`, `read_priv`, `write_priv`, `reply_priv`, `tail_priv`, `topfile`, `downfile`, `use_secret`, `use_category`, `usecate`, `usefile`, `usereply`, `usetail`, `width`, `page_num`, `read_list`, `list_cut`, `content_head`, `content_tail`, `insert_content`, `fileurl1`, `fileurl2`) VALUES
(13, 'gr_mall', 'basic', 'basic', '공지사항', 99, 99, 1, 1, 1, './board_head.php', './board_tail.php', 0, 0, '', 'Y', 'Y', 'Y', 100, 20, 1, 56, '', '', '', '', ''),
(20, 'gr_item', 'basic', 'basic', '공지사항', 9, 9, 1, 1, 1, '../mypage/board_head.php', '../mypage/board_tail.php', 0, 0, '', 'Y', 'Y', '', 890, 25, 1, 100, '', '', '', '', ''),
(21, 'gr_item', 'basic', 'basic', '질문과답변', 9, 9, 9, 1, 1, '../mypage/board_head.php', '../mypage/board_tail.php', 0, 0, '', '', 'Y', 'Y', 890, 20, 1, 100, '', '', '', '', ''),
(22, 'gr_home', 'basic', 'basic', '공지사항', 99, 99, 1, 1, 9, '../mypage/board_head.php', '../mypage/board_tail.php', 0, 0, '', 'Y', 'Y', 'Y', 100, 20, 1, 100, '', '', '', '', ''),
(36, 'gr_home', 'basic', 'basic', '질문과답변', 6, 6, 6, 1, 6, '../mypage/board_head.php', '../mypage/board_tail.php', 0, 0, '', 'Y', '', 'Y', 100, 20, 1, 100, '', '', '', '', ''),
(41, 'gr_mall', 'webzine', 'webzine', '갤러리게시판', 99, 9, 9, 9, 9, './board_head.php', './board_tail.php', 0, 0, '', 'Y', 'Y', 'Y', 100, 30, 1, 40, '', '', '', '', '');


DROP TABLE IF EXISTS `shop_board_group`;
CREATE TABLE IF NOT EXISTS `shop_board_group` (
  `gr_id` varchar(10) NOT NULL COMMENT '그룹ID',
  `gr_subject` varchar(255) NOT NULL COMMENT '그룹명',
  PRIMARY KEY  (`gr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='게시판그룹 테이블';

INSERT INTO `shop_board_group` (`gr_id`, `gr_subject`) VALUES
('gr_home', '가맹점'),
('gr_mall', '쇼핑몰'),
('gr_item', '입점사');


DROP TABLE IF EXISTS `shop_brand`;
CREATE TABLE IF NOT EXISTS `shop_brand` (
  `br_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  `br_name` varchar(255) NOT NULL COMMENT '브랜드명',
  `br_name_eng` varchar(255) NOT NULL COMMENT '브랜드영문명',
  `br_logo` varchar(255) NOT NULL COMMENT '브랜드로고',
  `br_user_yes` tinyint(4) NOT NULL default '0' COMMENT '가맹점등록시(1)',
  `br_time` datetime NOT NULL COMMENT '등록일시',
  `br_updatetime` datetime NOT NULL COMMENT '수정일시',
  PRIMARY KEY  (`br_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 브랜드 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_cart`;
CREATE TABLE IF NOT EXISTS `shop_cart` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `od_id` varchar(30) NOT NULL COMMENT '주문번호',
  `od_no` varchar(30) NOT NULL COMMENT '일련번호',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `ca_id` varchar(15) NOT NULL COMMENT '카테고리번호',
  `gs_id` int(11) NOT NULL default '0' COMMENT '상품주키',
  `ct_direct` varchar(100) NOT NULL COMMENT '구매자 고유쿠키값',
  `ct_price` int(11) NOT NULL default '0' COMMENT '상품금액',
  `ct_supply_price` int(11) NOT NULL default '0' COMMENT '공급가격',
  `ct_qty` int(11) NOT NULL default '0' COMMENT '수량',
  `ct_point` int(11) NOT NULL default '0' COMMENT '적립포인트',
  `io_id` varchar(255) NOT NULL COMMENT '옵션항목',
  `io_type` tinyint(4) NOT NULL default '0' COMMENT '옵션타입(필수:0, 선택:1)',
  `io_price` int(11) NOT NULL default '0' COMMENT '옵션가격',
  `ct_option` varchar(255) NOT NULL COMMENT '옵션명',
  `ct_send_cost` tinyint(4) NOT NULL default '0' COMMENT '배송비(선불/착불)여부',
  `ct_select` tinyint(4) NOT NULL default '0' COMMENT '주문전(0), 주문후(1)',
  `ct_time` datetime NOT NULL COMMENT '등록일시',
  `ct_ip` varchar(255) NOT NULL COMMENT '구매자IP',
  PRIMARY KEY  (`index_no`),
  KEY `member` (`mb_id`,`gs_id`),
  KEY `ct_select` (`ct_select`),
  KEY `ct_time` (`ct_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='장바구니 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE IF NOT EXISTS `shop_category` (
  `index_no` int(11) unsigned NOT NULL auto_increment COMMENT '주키',
  `catecode` varchar(15) NOT NULL COMMENT '카테고리번호',
  `upcate` varchar(12) NOT NULL COMMENT '상위카테고리번호(최상위는 빈값)',
  `catename` varchar(255) NOT NULL COMMENT '카테고리명',
  `cateimg1` varchar(255) NOT NULL COMMENT '카테고리 아이콘',
  `cateimg2` varchar(255) NOT NULL COMMENT '카테고리 아이콘 (ON)',
  `headimg` varchar(255) NOT NULL COMMENT '카테고리 상단배너',
  `headimgurl` varchar(255) NOT NULL COMMENT '카테고리 상단배너 URL',
  `caterank` int(11) NOT NULL default '0' COMMENT '정렬순서',
  `cateuse` tinyint(4) NOT NULL default '0' COMMENT '노출',
  `catehide` longtext NOT NULL COMMENT '감춤(가맹점ID)',
  PRIMARY KEY  (`index_no`),
  KEY `catecode` (`catecode`,`upcate`),
  KEY `cateuse` (`cateuse`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='카테고리 테이블' AUTO_INCREMENT=1;

INSERT INTO `shop_category` (`catecode`, `upcate`, `catename`, `cateimg1`, `cateimg2`, `headimg`, `headimgurl`, `caterank`, `cateuse`, `catehide`) VALUES
('001', '', '패션의류/잡화/뷰티', '', '', '', '', 1, 0, ''),
('001001', '001', '여성의류', '', '', '', '', 2, 0, ''),
('001002', '001', '남성의류', '', '', '', '', 1, 0, ''),
('001003', '001', '언더웨어', '', '', '', '', 3, 0, ''),
('001004', '001', '신발', '', '', '', '', 4, 0, ''),
('001005', '001', '가방/잡화', '', '', '', '', 5, 0, ''),
('001006', '001', '쥬얼리/시계', '', '', '', '', 6, 0, ''),
('001007', '001', '화장품/향수', '', '', '', '', 7, 0, ''),
('001008', '001', '바디/헤어', '', '', '', '', 8, 0, ''),
('002', '', '식품/생필품', '', '', '', '', 2, 0, ''),
('002001', '002', '신선식품', '', '', '', '', 1, 0, ''),
('002002', '002', '가공식품', '', '', '', '', 2, 0, ''),
('002003', '002', '커피/음료', '', '', '', '', 3, 0, ''),
('002004', '002', '세제/구강', '', '', '', '', 4, 0, ''),
('002005', '002', '화장지/물티슈/생리대', '', '', '', '', 5, 0, ''),
('003', '', '출산/유아동', '', '', '', '', 3, 0, ''),
('003001', '003', '기저귀/분유/유아식', '', '', '', '', 1, 0, ''),
('003002', '003', '육아용품', '', '', '', '', 2, 0, ''),
('003003', '003', '장난감', '', '', '', '', 3, 0, ''),
('003004', '003', '유아동의류', '', '', '', '', 4, 0, ''),
('003005', '003', '유아동신발/잡화', '', '', '', '', 5, 0, ''),
('004', '', '생활/건강', '', '', '', '', 4, 0, ''),
('004001', '004', '건강/의료용품', '', '', '', '', 1, 0, ''),
('004002', '004', '건강식품', '', '', '', '', 2, 0, ''),
('004003', '004', '운동용품', '', '', '', '', 3, 0, ''),
('005', '', '가구/인테리어', '', '', '', '', 5, 0, ''),
('005001', '005', '가구/DIY', '', '', '', '', 1, 0, ''),
('005002', '005', '침구/커튼', '', '', '', '', 2, 0, ''),
('005003', '005', '조명/인테리어', '', '', '', '', 3, 0, ''),
('005004', '005', '생활/욕실/수납용품', '', '', '', '', 4, 0, ''),
('005005', '005', '주방용품', '', '', '', '', 5, 0, ''),
('005006', '005', '꽃/이벤트용품', '', '', '', '', 6, 0, ''),
('006', '', '가전/디지털/컴퓨터', '', '', '', '', 6, 0, ''),
('006001', '006', '대형가전', '', '', '', '', 0, 0, ''),
('006002', '006', '계절가전', '', '', '', '', 1, 0, ''),
('006003', '006', '주방가전', '', '', '', '', 2, 0, ''),
('006004', '006', '생활/미용가전', '', '', '', '', 3, 0, ''),
('006005', '006', '카메라', '', '', '', '', 4, 0, ''),
('006006', '006', '음향기기', '', '', '', '', 5, 0, ''),
('006007', '006', '게임', '', '', '', '', 6, 0, ''),
('006008', '006', '휴대폰', '', '', '', '', 7, 0, ''),
('006009', '006', '태블릿', '', '', '', '', 8, 0, ''),
('006010', '006', '노트북/PC', '', '', '', '', 9, 0, ''),
('006011', '006', '모니터/프린터', '', '', '', '', 10, 0, ''),
('006012', '006', 'PC주변기기', '', '', '', '', 11, 0, ''),
('006013', '006', '저장장치', '', '', '', '', 12, 0, ''),
('007', '', '스포츠/레저/자동차/공구', '', '', '', '', 7, 0, ''),
('007001', '007', '휘트니스/수영', '', '', '', '', 1, 0, ''),
('007002', '007', '스포츠의류/운동화', '', '', '', '', 2, 0, ''),
('007003', '007', '골프클럽/의류/용품', '', '', '', '', 3, 0, ''),
('007004', '007', '등산/아웃도어', '', '', '', '', 4, 0, ''),
('007005', '007', '캠핑/낚시', '', '', '', '', 5, 0, ''),
('007006', '007', '구기/라켓', '', '', '', '', 6, 0, ''),
('007007', '007', '자전거/보드', '', '', '', '', 7, 0, ''),
('007008', '007', '자동차용품/블랙박스', '', '', '', '', 8, 0, ''),
('007009', '007', '타이어/오일/부품', '', '', '', '', 9, 0, ''),
('007010', '007', '공구/안전/산업용품', '', '', '', '', 10, 0, ''),
('008', '', '도서/여행/e쿠폰/취미', '', '', '', '', 8, 0, ''),
('008001', '008', '도서음반/e교육', '', '', '', '', 1, 0, ''),
('008002', '008', '여행/항공권', '', '', '', '', 2, 0, ''),
('008003', '008', '티켓', '', '', '', '', 3, 0, ''),
('008004', '008', 'e쿠폰/상품권', '', '', '', '', 4, 0, ''),
('008005', '008', '취미', '', '', '', '', 5, 0, '');


DROP TABLE IF EXISTS `shop_cert_history`;
CREATE TABLE IF NOT EXISTS `shop_cert_history` (
  `cr_id` int(11) NOT NULL auto_increment COMMENT '인증내역번호',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `cr_company` varchar(255) NOT NULL COMMENT '인증요청서비스',
  `cr_method` varchar(255) NOT NULL COMMENT '인증요청유형',
  `cr_ip` varchar(255) NOT NULL COMMENT '요청',
  `cr_date` date NOT NULL default '0000-00-00' COMMENT '요청일',
  `cr_time` time NOT NULL default '00:00:00' COMMENT '요청시간',
  PRIMARY KEY  (`cr_id`),
  KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='인증내역 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_config`;
CREATE TABLE IF NOT EXISTS `shop_config` (
  `admin_shop_url` varchar(255) NOT NULL COMMENT '대표도메인',
  `admin_reg_yes` tinyint(4) NOT NULL default '0' COMMENT '본사몰 회원가입 가능여부',
  `admin_reg_msg` text NOT NULL COMMENT '본사몰 회원가입 거부시 출력 할 경고메세지',
  `admin_seal` varchar(255) NOT NULL COMMENT '사업자인감',
  `shop_name` varchar(100) NOT NULL COMMENT '쇼핑몰명',
  `shop_name_us` varchar(100) NOT NULL COMMENT '쇼핑몰 영문명',
  `company_type` tinyint(4) NOT NULL default '0' COMMENT '사업자유형',
  `company_name` varchar(50) NOT NULL COMMENT '회사명',
  `company_saupja_no` varchar(30) NOT NULL COMMENT '사업자등록번호',
  `tongsin_no` varchar(30) NOT NULL COMMENT '통신판매업신고번호',
  `company_tel` varchar(30) NOT NULL COMMENT '대표전화번호',
  `company_fax` varchar(30) NOT NULL COMMENT '팩스번호',
  `company_item` varchar(100) NOT NULL COMMENT '업태',
  `company_service` varchar(100) NOT NULL COMMENT '종목',
  `company_owner` varchar(50) NOT NULL COMMENT '대표자명',
  `company_zip` varchar(5) NOT NULL COMMENT '사업장우편번호',
  `company_addr` varchar(255) NOT NULL COMMENT '사업장주소',
  `company_hours` varchar(255) NOT NULL COMMENT '상담가능시간',
  `company_lunch` varchar(255) NOT NULL COMMENT '점심시간',
  `company_close` varchar(255) NOT NULL COMMENT '휴무일',
  `info_name` varchar(50) NOT NULL COMMENT '정보책임자 이름',
  `info_email` varchar(255) NOT NULL COMMENT '정보책임자 e-mail',
  `head_title` varchar(255) NOT NULL COMMENT '브라우저 타이틀',
  `meta_author` varchar(255) NOT NULL COMMENT 'Author : 메타태그',
  `meta_description` varchar(255) NOT NULL COMMENT 'description : 메타태그',
  `meta_keywords` text NOT NULL COMMENT 'keywords : 메타태그',
  `add_meta` text NOT NULL COMMENT '추가 메타태그',
  `head_script` text NOT NULL COMMENT 'HEAD 내부 스크립트',
  `tail_script` text NOT NULL COMMENT 'BODY 내부 스크립트',
  `write_pages` int(11) NOT NULL default '0' COMMENT '페이지 표시 수',
  `mobile_pages` int(11) NOT NULL default '0' COMMENT '모바일 페이지 표시 수',
  `seller_reg_yes` tinyint(4) NOT NULL default '0' COMMENT '입점서비스 사용',
  `seller_reg_auto` tinyint(4) NOT NULL default '0' COMMENT '입점사 상품등록 관리자승인여부',
  `seller_mod_auto` tinyint(4) NOT NULL default '0' COMMENT '입점사 상품수정 관리자승인여부',
  `seller_reg_agree` longtext NOT NULL COMMENT '입점 가입약관',
  `seller_reg_guide` longtext NOT NULL COMMENT 'PC 입점 이용안내',
  `seller_reg_mobile_guide` longtext NOT NULL COMMENT '모바일 입점 이용안내',
  `delivery_method` tinyint(4) NOT NULL default '1' COMMENT '배송비유형',
  `delivery_price` int(11) NOT NULL default '0' COMMENT '기본배송비',
  `delivery_price2` int(11) NOT NULL default '0' COMMENT '조건부기본배송비',
  `delivery_minimum` int(11) NOT NULL default '0' COMMENT '조건부무료배송비',
  `delivery_company` text NOT NULL COMMENT '배송업체 기본정보',
  `register_point` int(11) NOT NULL default '0' COMMENT '회원가입 포인트',
  `partner_point` int(11) NOT NULL default '0' COMMENT '회원가입 추천인 포인트',
  `login_point` int(11) NOT NULL default '0' COMMENT '로그인 포인트',
  `usepoint` int(11) NOT NULL default '0' COMMENT '주문시 최소 결제포인트',
  `usepoint_yes` tinyint(4) NOT NULL default '0' COMMENT '주문시 포인트결제 사용',
  `mouseblock_yes` tinyint(4) NOT NULL default '0' COMMENT '드레그, 마우스 우클릭 차단',
  `shop_provision` longtext NOT NULL COMMENT '회원가입약관',
  `shop_private` longtext NOT NULL COMMENT '개인정보 수집 및 이용',
  `shop_policy` longtext NOT NULL COMMENT '개인정보처리방침',
  `shop_intro_yes` tinyint(4) NOT NULL default '0' COMMENT '폐쇄몰 사용여부',
  `cert_admin_yes` tinyint(4) NOT NULL default '0' COMMENT '폐쇄몰 회원인증',
  `cert_partner_yes` tinyint(4) NOT NULL default '0' COMMENT '폐쇄몰 가입인증 권한',
  `coupon_yes` tinyint(4) NOT NULL default '0' COMMENT '온라인쿠폰 사용',
  `gift_yes` tinyint(4) NOT NULL default '0' COMMENT '인쇄용쿠폰 사용',
  `baesong_cont1` text NOT NULL COMMENT '쇼핑몰 배송/교환/반품안내',
  `baesong_cont2` text NOT NULL COMMENT '모바일 배송/교환/반품안내',
  `prohibit_id` text NOT NULL COMMENT '가입불가 ID',
  `prohibit_email` text NOT NULL COMMENT '가입불가 e-mail',
  `possible_ip` text NOT NULL COMMENT '접근 가능 IP',
  `intercept_ip` text NOT NULL COMMENT '접근 차단 IP',
  `cf_cert_use` tinyint(4) NOT NULL default '0' COMMENT '본인인증사용여부',
  `cf_cert_ipin` varchar(255) NOT NULL default '' COMMENT '아이핀 본인인증',
  `cf_cert_hp` varchar(255) NOT NULL default '' COMMENT '휴대폰 본인인증',
  `cf_cert_kcb_cd` varchar(255) NOT NULL default '' COMMENT '코리아크레딧뷰로 KCB 회원사ID',
  `cf_cert_kcp_cd` varchar(255) NOT NULL default '' COMMENT 'NHN KCP 사이트코드',
  `cf_lg_mid` varchar(255) NOT NULL default '' COMMENT 'LG유플러스 상점아이디',
  `cf_lg_mert_key` varchar(255) NOT NULL default '' COMMENT 'LG유플러스 MERT KEY',
  `cf_cert_limit` int(11) NOT NULL default '0' COMMENT '본인인증 이용제한',
  `cf_cert_req` tinyint(4) NOT NULL default '0' COMMENT '본인인증 필수',
  `register_use_hp` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 핸드폰 사용',
  `register_req_hp` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 핸드폰 필수입력',
  `register_use_tel` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 전화번호 사용',
  `register_req_tel` tinyint(4) NOT NULL default '0' COMMENT ' 회원가입시 전화번호 필수입력',
  `register_use_addr` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 주소 사용',
  `register_req_addr` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 주소 필수입력',
  `partner_reg_yes` tinyint(4) NOT NULL default '0' COMMENT '가맹점모집 사용',
  `pf_sale_use` tinyint(4) NOT NULL default '0' COMMENT '상품 판매수수료 사용',
  `pf_sale_flag` tinyint(4) NOT NULL default '0' COMMENT '상품 판매수수료 유형',
  `pf_anew_use` tinyint(4) NOT NULL default '0' COMMENT '분양 추천수수료 사용',
  `pf_visit_use` tinyint(4) NOT NULL default '0' COMMENT '상점 접속수수료 사용',
  `pf_payment_type` tinyint(4) NOT NULL default '0' COMMENT '수수료 정산방법',
  `pf_payment` int(11) NOT NULL default '0' COMMENT '수수료 출금조건',
  `pf_payment_unit` int(11) NOT NULL default '0' COMMENT '수수료 출금단위',
  `pf_payment_tax` varchar(11) NOT NULL default '0' COMMENT '수수료 세액공제',
  `pf_payment_yes` tinyint(4) NOT NULL default '0' COMMENT '판매수수료 노출여부',
  `pf_stipulation_subj` varchar(255) NOT NULL COMMENT '가맹점 이용약관 제목',
  `pf_stipulation` text NOT NULL COMMENT '가맹점 이용약관',
  `pf_regulation_subj` varchar(255) NOT NULL COMMENT '가맹점 규정안내 제목',
  `pf_regulation` text NOT NULL COMMENT '가맹점 규정안내',
  `pf_basedomain` varchar(255) NOT NULL COMMENT '도메인 종류',
  `pf_auth_good` tinyint(4) NOT NULL default '0' COMMENT '상품판매 권한',
  `pf_auth_pg` tinyint(4) NOT NULL default '0' COMMENT 'PG결제연동 권한',
  `pf_auth_sms` tinyint(4) NOT NULL default '0' COMMENT 'SMS 문자설정 권한',
  `pf_expire_use` tinyint(4) NOT NULL default '0' COMMENT '관리비 사용',
  `pf_expire_term` int(11) NOT NULL default '0' COMMENT '관리비 연장기간 단위',
  `pf_login_no` tinyint(4) NOT NULL default '0' COMMENT '관리비 미납시 로그인차단',
  `pf_account_no` tinyint(4) NOT NULL default '0' COMMENT '관리비 미납시 수수료 출금신청 차단',
  `pf_session_no` tinyint(4) NOT NULL default '0' COMMENT '관리비 미납시 운영권한을 본사로 귀속',
  `pf_sale_benefit_dan` int(11) NOT NULL default '0' COMMENT '판매수수료 적립단계',
  `pf_sale_benefit_type` tinyint(4) NOT NULL default '0' COMMENT '판매수수료 적립유형',
  `pf_sale_benefit_2` varchar(255) NOT NULL COMMENT '2레벨 판매수수료',
  `pf_sale_benefit_3` varchar(255) NOT NULL COMMENT '3레벨 판매수수료',
  `pf_sale_benefit_4` varchar(255) NOT NULL COMMENT '4레벨 판매수수료',
  `pf_sale_benefit_5` varchar(255) NOT NULL COMMENT '5레벨 판매수수료',
  `pf_sale_benefit_6` varchar(255) NOT NULL COMMENT '6레벨 판매수수료',
  `pf_anew_benefit_dan` int(11) NOT NULL default '0' COMMENT '추천수수료 적립단계',
  `pf_anew_benefit_type` tinyint(4) default '0' COMMENT '추천수수료 적립유형',
  `pf_anew_benefit_2` varchar(255) NOT NULL COMMENT '2레벨 추천수수료',
  `pf_anew_benefit_3` varchar(255) NOT NULL COMMENT '3레벨 추천수수료',
  `pf_anew_benefit_4` varchar(255) NOT NULL COMMENT '4레벨 추천수수료',
  `pf_anew_benefit_5` varchar(255) NOT NULL COMMENT '5레벨 추천수수료',
  `pf_anew_benefit_6` varchar(255) NOT NULL COMMENT '6레벨 추천수수료'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 환경설정1 테이블';

INSERT INTO `shop_config` (`admin_shop_url`, `admin_reg_yes`, `admin_reg_msg`, `admin_seal`, `shop_name`, `shop_name_us`, `company_type`, `company_name`, `company_saupja_no`, `tongsin_no`, `company_tel`, `company_fax`, `company_item`, `company_service`, `company_owner`, `company_zip`, `company_addr`, `company_hours`, `company_lunch`, `company_close`, `info_name`, `info_email`, `head_title`, `meta_author`, `meta_description`, `meta_keywords`, `add_meta`, `head_script`, `tail_script`, `write_pages`, `mobile_pages`, `seller_reg_yes`, `seller_reg_auto`, `seller_mod_auto`, `seller_reg_agree`, `seller_reg_guide`, `seller_reg_mobile_guide`, `delivery_method`, `delivery_price`, `delivery_price2`, `delivery_minimum`, `delivery_company`, `register_point`, `partner_point`, `login_point`, `usepoint`, `usepoint_yes`, `mouseblock_yes`, `shop_provision`, `shop_private`, `shop_policy`, `shop_intro_yes`, `cert_admin_yes`, `cert_partner_yes`, `coupon_yes`, `gift_yes`, `baesong_cont1`, `baesong_cont2`, `prohibit_id`, `prohibit_email`, `possible_ip`, `intercept_ip`, `cf_cert_use`, `cf_cert_ipin`, `cf_cert_hp`, `cf_cert_kcb_cd`, `cf_cert_kcp_cd`, `cf_lg_mid`, `cf_lg_mert_key`, `cf_cert_limit`, `cf_cert_req`, `register_use_hp`, `register_req_hp`, `register_use_tel`, `register_req_tel`, `register_use_addr`, `register_req_addr`, `partner_reg_yes`, `pf_sale_use`, `pf_sale_flag`, `pf_anew_use`, `pf_visit_use`, `pf_payment_type`, `pf_payment`, `pf_payment_unit`, `pf_payment_tax`, `pf_payment_yes`, `pf_stipulation_subj`, `pf_stipulation`, `pf_regulation_subj`, `pf_regulation`, `pf_basedomain`, `pf_auth_good`, `pf_auth_pg`, `pf_auth_sms`, `pf_expire_use`, `pf_expire_term`, `pf_login_no`, `pf_account_no`, `pf_session_no`, `pf_sale_benefit_dan`, `pf_sale_benefit_type`, `pf_sale_benefit_2`, `pf_sale_benefit_3`, `pf_sale_benefit_4`, `pf_sale_benefit_5`, `pf_sale_benefit_6`, `pf_anew_benefit_dan`, `pf_anew_benefit_type`, `pf_anew_benefit_2`, `pf_anew_benefit_3`, `pf_anew_benefit_4`, `pf_anew_benefit_5`, `pf_anew_benefit_6`) VALUES
('omnimall.co.kr', 0, '본사 쇼핑몰에서는 회원가입이 불가능 합니다.\r\n[아이디.omnimall.co.kr]으로 접속하셔야 가입이 가능합니다.', 'CxmnP4zmaa3VsY1xmULZxhBTPUgE69.png', '행복을 주는 쇼핑몰!', 'Happy shopping', 0, '옴니솔루션', '000-00-00000', '2017-서울강남-0000호', '02-0000-0000', '02-0000-0000', '서비스업,도소매', '전자상거래업', '홍길동', '12345', 'OO도 OO시 OO구 OO동 123-45', '오전10시~오후06시', '오후12시~오후1시', '토요일,공휴일 휴무', '임꺽정', 'help@domain.com', '옴니솔루션 - PHP 웹솔루션 전문 개발업체', '옴니솔루션, Omni', '옴니솔루션 - PHP 웹솔루션 전문 개발업체', 'PHP, 쇼핑몰솔루션, 독립형쇼핑몰, 입점형쇼핑몰, 독립몰, 입점몰, 몰인몰, 분양쇼핑몰, 분양몰, 프랜차이즈몰, 홈빌더', '', '', '', 10, 5, 1, 1, 1, '해당 쇼핑몰에 맞는 입점 가입약관을 입력합니다.', '입점 이용안내 내용 또는 이미지를 입력해주세요.', '입점 이용안내 내용 또는 이미지를 입력해주세요.', 1, 1800, 1800, 80000, 'KG로지스|http://www.kglogis.co.kr/contents/waybill.jsp?item_no=,KGB택배|http://www.kgbls.co.kr/sub5/trace.asp?f_slipno=,KG옐로우캡택배|http://www.yellowcap.co.kr/custom/inquiry_result.asp?invoice_no=,CVSnet편의점택배|http://was.cvsnet.co.kr/_ver2/board/ctod_status.jsp?invoice_no=,CJ대한통운|https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=,롯데택배(구현대택배)|https://www.lotteglogis.com/open/tracking?InvNo=,한진택배|http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=,이노지스택배|http://www.innogis.co.kr/tracking_view.asp?invoice=,우체국|http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=,로젠택배|http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx?gubun=slipno&slipno=,동부택배|http://www.dongbups.com/delivery/delivery_search_view.jsp?item_no=,대신택배|http://home.daesinlogistics.co.kr/daesin/jsp/d_freight_chase/d_general_process2.jsp?billno1=,경동택배|http://www.kdexp.com/sub3_shipping.asp?stype=1&p_item=', 3000, 0, 0, 5000, 1, 0, '해당 쇼핑몰에 맞는 회원가입약관을 입력합니다.', '해당 쇼핑몰에 맞는 개인정보 수집 및 이용을 입력합니다.', '해당 쇼핑몰에 맞는 개인정보처리방침을 입력합니다.', 0, 0, 0, 1, 1, '쇼핑몰 배송/교환/반품안내', '모바일 배송/교환/반품안내', 'admin,administrator,webmaster,sysop,manager,root,su,guest,www', 'hanmail.net', '', '', 0, '', '', '', '', '', '', 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 30000, 1000, '3.3', 1, '소프트웨어(쇼핑몰 솔루션) 사용권 계약서/이용약관(EULA)', '', '불법홍보 금지 및 규정안내', '', 'asia|info|name|mobi|com|net|org|biz|tel|xxx|kr|co|so|me|eu|cc|or|pe|ne|re|tv|jp|tw|in|shop', 2, 2, 1, 1, 12, 1, 1, 1, 2, 0, '00', '00', '00', '2010', '105', 1, 1, '0', '0', '0', '20000', '10000');


DROP TABLE IF EXISTS `shop_content`;
CREATE TABLE IF NOT EXISTS `shop_content` (
  `co_id` int(11) unsigned NOT NULL auto_increment COMMENT '고유값',
  `co_subject` varchar(255) NOT NULL COMMENT '제목',
  `co_content` longtext NOT NULL COMMENT 'PC 내용',
  `co_mobile_content` longtext NOT NULL COMMENT '모바일 내용',
  PRIMARY KEY  (`co_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='개별 페이지관리 테이블' AUTO_INCREMENT=3;

INSERT INTO `shop_content` (`co_id`, `co_subject`, `co_content`, `co_mobile_content`) VALUES
(1, '회사소개', '회사소개 내용 또는 이미지를 입력해주세요.', '회사소개 내용 또는 이미지를 입력해주세요.'),
(2, '이용안내', '이용안내 내용 또는 이미지를 입력해주세요.', '이용안내 내용 또는 이미지를 입력해주세요.');


DROP TABLE IF EXISTS `shop_coupon`;
CREATE TABLE IF NOT EXISTS `shop_coupon` (
  `cp_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `cp_type` tinyint(4) NOT NULL default '0' COMMENT '쿠폰유형',
  `cp_dlimit` varchar(11) NOT NULL COMMENT '누적제한',
  `cp_dlevel` tinyint(4) NOT NULL default '10' COMMENT '레벨제한',
  `cp_subject` varchar(255) NOT NULL COMMENT '쿠폰명',
  `cp_explan` varchar(255) NOT NULL COMMENT '설명',
  `cp_use` tinyint(4) NOT NULL default '0' COMMENT '사용여부',
  `cp_download` tinyint(4) NOT NULL default '0' COMMENT '쿠폰발급 시점',
  `cp_overlap` tinyint(4) NOT NULL default '0' COMMENT '중복발급 여부',
  `cp_sale_type` tinyint(4) NOT NULL default '0' COMMENT '해택유형',
  `cp_sale_percent` int(11) NOT NULL default '0' COMMENT '해택(할인률)',
  `cp_sale_amt_max` int(11) NOT NULL default '0' COMMENT '해택(최대 할인금액)',
  `cp_sale_amt` int(11) NOT NULL default '0' COMMENT '해택(할인금액)',
  `cp_dups` tinyint(4) NOT NULL default '0' COMMENT '중복사용 여부',
  `cp_use_sex` char(1) NOT NULL COMMENT '성별구분',
  `cp_use_sage` varchar(4) NOT NULL COMMENT '연령대 시작',
  `cp_use_eage` varchar(4) NOT NULL COMMENT '연령대 끝',
  `cp_week_day` varchar(100) NOT NULL COMMENT '쿠폰발행 요일',
  `cp_pub_1_use` tinyint(4) NOT NULL default '0' COMMENT '1.발행시간 사용',
  `cp_pub_shour1` char(2) NOT NULL COMMENT '1.발행시간 시작',
  `cp_pub_ehour1` char(2) NOT NULL COMMENT '1.발행시간 끝',
  `cp_pub_1_cnt` int(11) NOT NULL default '0' COMMENT '1.발행매수',
  `cp_pub_1_down` int(11) NOT NULL default '0' COMMENT '1.다운로드수',
  `cp_pub_2_use` tinyint(4) NOT NULL default '0' COMMENT '2.발행시간 사용',
  `cp_pub_shour2` char(2) NOT NULL COMMENT '2.발행시간 시작',
  `cp_pub_ehour2` char(2) NOT NULL COMMENT '2.발행시간 끝',
  `cp_pub_2_cnt` int(11) NOT NULL default '0' COMMENT '2.발행매수',
  `cp_pub_2_down` int(11) NOT NULL default '0' COMMENT '2.다운로드수',
  `cp_pub_3_use` tinyint(4) NOT NULL default '0' COMMENT '3.발행시간 사용',
  `cp_pub_shour3` char(2) NOT NULL COMMENT '3.발행시간 시작',
  `cp_pub_ehour3` char(2) NOT NULL COMMENT '3.발행시간 끝',
  `cp_pub_3_cnt` int(11) NOT NULL default '0' COMMENT '3.발행매수',
  `cp_pub_3_down` int(11) NOT NULL default '0' COMMENT '3.다운로드수',
  `cp_pub_sdate` varchar(10) NOT NULL COMMENT '쿠폰발행 기간 시작',
  `cp_pub_edate` varchar(10) NOT NULL COMMENT '쿠폰발행 기간 끝',
  `cp_pub_sday` varchar(11) NOT NULL COMMENT '쿠폰발행 기간 (생일) 시작',
  `cp_pub_eday` varchar(11) NOT NULL COMMENT '쿠폰발행 기간 (생일) 끝',
  `cp_inv_type` tinyint(4) NOT NULL default '0' COMMENT '쿠폰유효 기간 유형',
  `cp_inv_sdate` varchar(10) NOT NULL COMMENT '쿠폰유효 기간 시작',
  `cp_inv_edate` varchar(10) NOT NULL COMMENT '쿠폰유효 기간 끝',
  `cp_inv_shour1` char(2) NOT NULL COMMENT '쿠폰유효 유효시간 시작',
  `cp_inv_shour2` char(2) NOT NULL COMMENT '쿠폰유효 유효시간 끝',
  `cp_inv_day` varchar(11) NOT NULL COMMENT '쿠폰발급일로부터 제한일',
  `cp_low_amt` int(11) NOT NULL default '0' COMMENT '금액제한',
  `cp_use_part` tinyint(4) NOT NULL default '0' COMMENT '사용가능대상',
  `cp_use_goods` text NOT NULL COMMENT '쿠폰 상품',
  `cp_use_category` text NOT NULL COMMENT '쿠폰 카테고리',
  `cp_odr_cnt` int(11) NOT NULL default '0' COMMENT '주문수',
  `cp_wdate` datetime NOT NULL COMMENT '등록일시',
  `cp_udate` datetime NOT NULL COMMENT '수정일시',
  PRIMARY KEY  (`cp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='온라인 쿠폰 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_coupon_log`;
CREATE TABLE IF NOT EXISTS `shop_coupon_log` (
  `lo_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(50) NOT NULL COMMENT '회원아이디',
  `mb_name` varchar(50) NOT NULL COMMENT '회원명',
  `mb_use` tinyint(4) NOT NULL default '0' COMMENT '회원사용유무',
  `od_no` varchar(20) NOT NULL COMMENT '주문일련번호',
  `cp_id` int(11) NOT NULL default '0' COMMENT '쿠폰주키',
  `cp_type` tinyint(4) NOT NULL default '0' COMMENT '쿠폰유형',
  `cp_dlimit` varchar(11) NOT NULL COMMENT '누적제한',
  `cp_dlevel` tinyint(4) NOT NULL default '10' COMMENT '레벨제한',
  `cp_subject` varchar(255) NOT NULL COMMENT '쿠폰명',
  `cp_explan` varchar(255) NOT NULL COMMENT '설명',
  `cp_use` tinyint(4) NOT NULL default '0' COMMENT '사용여부',
  `cp_download` tinyint(4) NOT NULL default '0' COMMENT '쿠폰발급 시점',
  `cp_overlap` tinyint(4) NOT NULL default '0' COMMENT '중복발급 여부',
  `cp_sale_type` tinyint(4) NOT NULL default '0' COMMENT '해택유형',
  `cp_sale_percent` int(11) NOT NULL default '0' COMMENT '해택(할인률)',
  `cp_sale_amt_max` int(11) NOT NULL default '0' COMMENT '해택(최대 할인금액)',
  `cp_sale_amt` int(11) NOT NULL default '0' COMMENT '해택(할인금액)',
  `cp_dups` tinyint(4) NOT NULL default '0' COMMENT '중복사용 여부',
  `cp_use_sex` char(1) NOT NULL COMMENT '성별구분',
  `cp_use_sage` varchar(4) NOT NULL COMMENT '연령대 시작',
  `cp_use_eage` varchar(4) NOT NULL COMMENT '연령대 끝',
  `cp_week_day` varchar(100) NOT NULL COMMENT '쿠폰발행 요일',
  `cp_pub_1_use` tinyint(4) NOT NULL default '0' COMMENT '1.발행시간 사용',
  `cp_pub_shour1` char(2) NOT NULL COMMENT '1.발행시간 시작',
  `cp_pub_ehour1` char(2) NOT NULL COMMENT '1.발행시간 끝',
  `cp_pub_1_cnt` int(11) NOT NULL default '0' COMMENT '1.발행매수',
  `cp_pub_1_down` int(11) NOT NULL default '0' COMMENT '1.다운로드수',
  `cp_pub_2_use` tinyint(4) NOT NULL default '0' COMMENT '2.발행시간 사용',
  `cp_pub_shour2` char(2) NOT NULL COMMENT '2.발행시간 시작',
  `cp_pub_ehour2` char(2) NOT NULL COMMENT '2.발행시간 끝',
  `cp_pub_2_cnt` int(11) NOT NULL default '0' COMMENT '2.발행매수',
  `cp_pub_2_down` int(11) NOT NULL default '0' COMMENT '2.다운로드수',
  `cp_pub_3_use` tinyint(4) NOT NULL default '0' COMMENT '3.발행시간 사용',
  `cp_pub_shour3` char(2) NOT NULL COMMENT '3.발행시간 시작',
  `cp_pub_ehour3` char(2) NOT NULL COMMENT '3.발행시간 끝',
  `cp_pub_3_cnt` int(11) NOT NULL default '0' COMMENT '3.발행매수',
  `cp_pub_3_down` int(11) NOT NULL default '0' COMMENT '3.다운로드수',
  `cp_pub_sdate` varchar(10) NOT NULL COMMENT '쿠폰발행 기간 시작',
  `cp_pub_edate` varchar(10) NOT NULL COMMENT '쿠폰발행 기간 끝',
  `cp_pub_sday` varchar(11) NOT NULL COMMENT '쿠폰발행 기간 (생일) 시작',
  `cp_pub_eday` varchar(11) NOT NULL COMMENT '쿠폰발행 기간 (생일) 끝',
  `cp_inv_type` tinyint(4) NOT NULL default '0' COMMENT '쿠폰유효 기간 유형',
  `cp_inv_sdate` varchar(10) NOT NULL COMMENT '쿠폰유효 기간 시작',
  `cp_inv_edate` varchar(10) NOT NULL COMMENT '쿠폰유효 기간 끝',
  `cp_inv_shour1` char(2) NOT NULL COMMENT '쿠폰유효 유효시간 시작',
  `cp_inv_shour2` char(2) NOT NULL COMMENT '쿠폰유효 유효시간 끝',
  `cp_inv_day` varchar(11) NOT NULL COMMENT '쿠폰발급일로부터 제한일',
  `cp_low_amt` int(11) NOT NULL default '0' COMMENT '금액제한',
  `cp_use_part` tinyint(4) NOT NULL default '0' COMMENT '사용가능대상',
  `cp_use_goods` text NOT NULL COMMENT '쿠폰 상품',
  `cp_use_category` text NOT NULL COMMENT '쿠폰 카테고리',
  `cp_wdate` datetime NOT NULL COMMENT '등록일시',
  `cp_udate` datetime NOT NULL COMMENT '수정일시',
  PRIMARY KEY  (`lo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='온라인 쿠폰 사용기록 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_default`;
CREATE TABLE IF NOT EXISTS `shop_default` (
  `de_bank_use` tinyint(4) NOT NULL default '0' COMMENT '무통장결제 사용',
  `de_card_use` tinyint(4) NOT NULL default '0' COMMENT '신용카드결제 사용',
  `de_iche_use` tinyint(4) NOT NULL default '0' COMMENT '계좌이체결제 사용',
  `de_vbank_use` tinyint(4) NOT NULL default '0' COMMENT '가상계좌결제 사용',
  `de_kakao_use` tinyint(4) NOT NULL default '0' COMMENT '카카오결제 사용',
  `de_hp_use` tinyint(4) NOT NULL default '0' COMMENT '휴대폰결제 사용',
  `de_card_test` tinyint(4) NOT NULL default '0' COMMENT '결제 테스트',
  `de_pg_service` varchar(255) NOT NULL COMMENT '결제대행사',
  `de_tax_flag_use` tinyint(4) NOT NULL default '0' COMMENT '복합과세 결제 사용',
  `de_taxsave_use` tinyint(4) NOT NULL default '0' COMMENT '현금영수증 발급사용',
  `de_card_noint_use` tinyint(4) NOT NULL default '0' COMMENT '신용카드 무이자할부사용',
  `de_easy_pay_use` tinyint(4) NOT NULL default '0' COMMENT 'PG사 간편결제 버튼사용',
  `de_escrow_use` tinyint(4) NOT NULL default '0' COMMENT '에스크로 사용',
  `de_kcp_mid` varchar(255) NOT NULL COMMENT 'NHN KCP SITE CODE',
  `de_kcp_site_key` varchar(255) NOT NULL COMMENT 'NHN KCP SITE KEY',
  `de_inicis_mid` varchar(255) NOT NULL COMMENT 'KG이니시스 상점아이디',
  `de_inicis_admin_key` varchar(255) NOT NULL COMMENT 'KG이니시스 키패스워드',
  `de_inicis_sign_key` varchar(255) NOT NULL COMMENT 'KG이니시스 웹결제 사인키',
  `de_samsung_pay_use` tinyint(4) NOT NULL default '0' COMMENT 'KG이니시스 삼성페이 버튼 사용',
  `de_lg_mid` varchar(255) NOT NULL COMMENT 'LG유플러스 상점아이디',
  `de_lg_mert_key` varchar(255) NOT NULL COMMENT 'LG유플러스 MertKey',
  `de_kakaopay_admin_key` varchar(255) NOT NULL COMMENT '카카오페이 Admin키',
  `de_kakaopay_cid` varchar(255) NOT NULL COMMENT '카카오페이 CID',
  `de_naverpay_mid` varchar(255) NOT NULL COMMENT '네이버페이 가맹점 아이디',
  `de_naverpay_cert_key` varchar(255) NOT NULL COMMENT '네이버페이 가맹점 인증키',
  `de_naverpay_button_key` varchar(255) NOT NULL COMMENT '네이버페이 버튼 인증키',
  `de_naverpay_test` tinyint(4) NOT NULL default '0' COMMENT '네이버페이 테스트결제',
  `de_naverpay_mb_id` varchar(255) NOT NULL COMMENT '네이버페이 결제테스트 아이디',
  `de_naverpay_sendcost` varchar(255) NOT NULL COMMENT '네이버페이 추가배송비 안내',
  `de_bank_account` text NOT NULL COMMENT '무통장입금계좌',
  `de_wish_keep_term` int(11) NOT NULL default '0' COMMENT '찜 보관일수',
  `de_cart_keep_term` int(11) NOT NULL default '0' COMMENT '장바구니 보관일수',
  `de_misu_keep_term` int(11) NOT NULL default '0' COMMENT '미입금 주문내역  보관일수',
  `de_final_keep_term` int(11) NOT NULL default '0' COMMENT '자동구매확정일',
  `de_optimize_date` date NOT NULL default '0000-00-00' COMMENT '자동업데이트 처리일',
  `de_review_wr_use` tinyint(4) NOT NULL default '0' COMMENT '구매후기) 작성된 분양몰에서만 노출',
  `de_board_wr_use` tinyint(4) NOT NULL default '0' COMMENT '게시판글) 작성된 분양몰에서만 노출',
  `de_logo_wpx` int(11) NOT NULL default '0' COMMENT 'PC 쇼핑몰로고 가로크기',
  `de_logo_hpx` int(11) NOT NULL default '0' COMMENT 'PC 쇼핑몰로고 세로크기',
  `de_mobile_logo_wpx` int(11) NOT NULL default '0' COMMENT '모바일 쇼핑몰로고 가로크기',
  `de_mobile_logo_hpx` int(11) NOT NULL default '0' COMMENT '모바일 쇼핑몰로고 세로크기',
  `de_slider_wpx` int(11) NOT NULL default '0' COMMENT 'PC 메인배너 가로크기',
  `de_slider_hpx` int(11) NOT NULL default '0' COMMENT 'PC 메인배너 세로크기',
  `de_mobile_slider_wpx` int(11) NOT NULL default '0' COMMENT '모바일 메인배너 가로크기',
  `de_mobile_slider_hpx` int(11) NOT NULL default '0' COMMENT '모바일 메인배너 세로크기',
  `de_item_small_wpx` int(11) NOT NULL default '0' COMMENT '상품 소이미지 가로크기',
  `de_item_small_hpx` int(11) NOT NULL default '0' COMMENT '상품 소이미지 세로크기',
  `de_item_medium_wpx` int(11) NOT NULL default '0' COMMENT '상품 중이미지 가로크기',
  `de_item_medium_hpx` int(11) NOT NULL default '0' COMMENT '상품 중이미지 세로크기',
  `de_sns_login_use` tinyint(4) NOT NULL default '0' COMMENT '소셜네트워크 로그인 사용',
  `de_naver_appid` varchar(255) NOT NULL COMMENT '네이버 Client ID',
  `de_naver_secret` varchar(255) NOT NULL COMMENT '네이버 Client Secret',
  `de_facebook_appid` varchar(255) NOT NULL COMMENT '페이스북 앱 ID',
  `de_facebook_secret` varchar(255) NOT NULL COMMENT '페이스북 앱 Secret',
  `de_kakao_rest_apikey` varchar(255) NOT NULL COMMENT '카카오 REST API Key',
  `de_googl_shorturl_apikey` varchar(255) NOT NULL COMMENT '구글 짧은주소 API Key',
  `de_insta_url` varchar(255) NOT NULL COMMENT '인스타그램)URL',
  `de_insta_client_id` varchar(255) NOT NULL COMMENT '인스타그램)CLIENT ID',
  `de_insta_redirect_uri` varchar(255) NOT NULL COMMENT '인스타그램)Valid redirect URIs',
  `de_insta_access_token` varchar(255) NOT NULL COMMENT '인스타그램)ACCESS_TOKEN',
  `de_sns_facebook` varchar(255) NOT NULL COMMENT 'SNS URL)FACEBOOK',
  `de_sns_twitter` varchar(255) NOT NULL COMMENT 'SNS URL)TWITTER',
  `de_sns_instagram` varchar(255) NOT NULL COMMENT 'SNS URL)INSTAGRAM',
  `de_sns_pinterest` varchar(255) NOT NULL COMMENT 'SNS URL)PINTEREST',
  `de_sns_naverblog` varchar(255) NOT NULL COMMENT 'SNS URL)NAVER BLOG',
  `de_sns_naverband` varchar(255) NOT NULL COMMENT 'SNS URL)NAVER BAND',
  `de_sns_kakaotalk` varchar(255) NOT NULL COMMENT 'SNS URL)KAKAOTALK',
  `de_sns_kakaostory` varchar(255) NOT NULL COMMENT 'SNS URL)KAKAOSTORY',
  `de_maintype_title` varchar(255) NOT NULL COMMENT '카테고리별 베스트 타이틀',
  `de_maintype_best` text NOT NULL COMMENT '카테고리별 베스트 상품',
  `de_pname_use_1` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용1',
  `de_pname_use_2` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용2',
  `de_pname_use_3` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용3',
  `de_pname_use_4` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용4',
  `de_pname_use_5` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용5',
  `de_pname_use_6` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용6',
  `de_pname_use_7` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용7',
  `de_pname_use_8` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용8',
  `de_pname_1` varchar(255) NOT NULL COMMENT '메뉴명1',
  `de_pname_2` varchar(255) NOT NULL COMMENT '메뉴명2',
  `de_pname_3` varchar(255) NOT NULL COMMENT '메뉴명3',
  `de_pname_4` varchar(255) NOT NULL COMMENT '메뉴명4',
  `de_pname_5` varchar(255) NOT NULL COMMENT '메뉴명5',
  `de_pname_6` varchar(255) NOT NULL COMMENT '메뉴명6',
  `de_pname_7` varchar(255) NOT NULL COMMENT '메뉴명7',
  `de_pname_8` varchar(255) NOT NULL COMMENT '메뉴명8'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 환경설정2 테이블';

INSERT INTO `shop_default` (`de_bank_use`, `de_card_use`, `de_iche_use`, `de_vbank_use`, `de_hp_use`, `de_card_test`, `de_pg_service`, `de_tax_flag_use`, `de_taxsave_use`, `de_card_noint_use`, `de_easy_pay_use`, `de_escrow_use`, `de_kcp_mid`, `de_kcp_site_key`, `de_inicis_mid`, `de_inicis_admin_key`, `de_inicis_sign_key`, `de_samsung_pay_use`, `de_lg_mid`, `de_lg_mert_key`, `de_kakaopay_admin_key`, `de_kakaopay_cid`, `de_naverpay_mid`, `de_naverpay_cert_key`, `de_naverpay_button_key`, `de_naverpay_test`, `de_naverpay_mb_id`, `de_naverpay_sendcost`, `de_bank_account`, `de_wish_keep_term`, `de_cart_keep_term`, `de_misu_keep_term`, `de_final_keep_term`, `de_optimize_date`, `de_review_wr_use`, `de_board_wr_use`, `de_logo_wpx`, `de_logo_hpx`, `de_mobile_logo_wpx`, `de_mobile_logo_hpx`, `de_slider_wpx`, `de_slider_hpx`, `de_mobile_slider_wpx`, `de_mobile_slider_hpx`, `de_item_small_wpx`, `de_item_small_hpx`, `de_item_medium_wpx`, `de_item_medium_hpx`, `de_sns_login_use`, `de_naver_appid`, `de_naver_secret`, `de_facebook_appid`, `de_facebook_secret`, `de_kakao_rest_apikey`, `de_googl_shorturl_apikey`, `de_insta_url`, `de_insta_client_id`, `de_insta_redirect_uri`, `de_insta_access_token`, `de_sns_facebook`, `de_sns_twitter`, `de_sns_instagram`, `de_sns_pinterest`, `de_sns_naverblog`, `de_sns_naverband`, `de_sns_kakaotalk`, `de_sns_kakaostory`, `de_maintype_title`, `de_maintype_best`, `de_pname_use_1`, `de_pname_use_2`, `de_pname_use_3`, `de_pname_use_4`, `de_pname_use_5`, `de_pname_use_6`, `de_pname_use_7`, `de_pname_use_8`, `de_pname_1`, `de_pname_2`, `de_pname_3`, `de_pname_4`, `de_pname_5`, `de_pname_6`, `de_pname_7`, `de_pname_8`) VALUES
(1, 1, 1, 1, 1, 1, 'inicis', 1, 1, 0, 1, 1, 'T0000', '3grptw1.zW0GSo4PQdaGvsF__', 'INIpayTest', '1111', 'SU5JTElURV9UUklQTEVERVNfS0VZU1RS', 1, 'dacomst7', '95160cce09854ef44d2edb2bfb05f9f3', '', '', '', '', '', 1, 'naverpay', '제주도 3,000원 추가, 제주도 외 도서·산간 지역 5,000원 추가', '', 7, 7, 7, 7, '2019-06-27', 0, 0, 160, 60, 450, 120, 1000, 400, 960, 720, 400, 400, 400, 400, 0, '', '', '', '', '', '', '', '', '', '', 'https://www.facebook.com', 'https://twitter.com', 'https://www.instagram.com', 'https://www.pinterest.co.kr', 'https://blog.naver.com', 'https://band.us/ko', 'https://www.kakaocorp.com/service/KakaoTalk?lang=ko', 'https://story.kakao.com', '카테고리별 베스트', '', 1, 1, 1, 1, 1, 1, 1, 1, '쇼핑특가', '베스트셀러', '신상품', '인기상품', '추천상품', '브랜드샵', '기획전', '타임세일');


DROP TABLE IF EXISTS `shop_faq`;
CREATE TABLE IF NOT EXISTS `shop_faq` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `cate` int(11) NOT NULL default '0' COMMENT '분류',
  `subject` varchar(255) NOT NULL COMMENT '제목',
  `memo` text NOT NULL COMMENT '내용',
  `wdate` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY  (`index_no`),
  KEY `cate` (`cate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='자주하는질문 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_faq_cate`;
CREATE TABLE IF NOT EXISTS `shop_faq_cate` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `catename` varchar(50) NOT NULL COMMENT '분류명',
  PRIMARY KEY  (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='자주하는질문 분류 테이블' AUTO_INCREMENT=8;

INSERT INTO `shop_faq_cate` (`index_no`, `catename`) VALUES
(1, '배송문의'),
(2, '취소/교환/반품'),
(3, '환불관련'),
(4, '주문/결제'),
(5, '쿠폰/포인트'),
(6, '회원관련'),
(7, '기타');


DROP TABLE IF EXISTS `shop_gift`;
CREATE TABLE IF NOT EXISTS `shop_gift` (
  `no` int(11) NOT NULL auto_increment COMMENT '주키',
  `gr_id` varchar(20) NOT NULL COMMENT '그룹아이디',
  `gr_subject` varchar(255) NOT NULL COMMENT '쿠폰명',
  `gr_price` int(11) NOT NULL default '0' COMMENT '쿠폰금액',
  `gr_sdate` varchar(10) NOT NULL COMMENT '쿠폰사용 시작일',
  `gr_edate` varchar(10) NOT NULL COMMENT '쿠폰사용 종료일',
  `gi_num` varchar(255) NOT NULL COMMENT '쿠폰번호',
  `gi_use` tinyint(4) NOT NULL default '0' COMMENT '쿠폰사용여부',
  `mb_id` varchar(50) NOT NULL COMMENT '회원아이디',
  `mb_name` varchar(50) NOT NULL COMMENT '회원명',
  `mb_wdate` datetime NOT NULL COMMENT '회원쿠폰사용일시',
  PRIMARY KEY  (`no`),
  KEY `gr_id` (`gr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='오프라인 쿠폰 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_gift_group`;
CREATE TABLE IF NOT EXISTS `shop_gift_group` (
  `gr_id` varchar(20) NOT NULL COMMENT '일련번호',
  `gr_subject` varchar(255) NOT NULL COMMENT '쿠폰명',
  `gr_explan` varchar(255) NOT NULL COMMENT '설명',
  `gr_price` int(11) NOT NULL default '0' COMMENT '발행금액',
  `gr_wdate` date NOT NULL COMMENT '등록일',
  `gr_sdate` varchar(10) NOT NULL COMMENT '사용시작일',
  `gr_edate` varchar(10) NOT NULL COMMENT '사용종료일',
  `use_gift` tinyint(4) NOT NULL default '0' COMMENT '쿠폰발행방식',
  PRIMARY KEY  (`gr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='오프라인 쿠폰 그룹 테이블';


DROP TABLE IF EXISTS `shop_goods`;
CREATE TABLE IF NOT EXISTS `shop_goods` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '판매자ID',
  `mb_name` varchar(255) NOT NULL COMMENT '판매자명',
  `ca_id` varchar(15) NOT NULL COMMENT '대표분류',
  `ca_id2` varchar(15) NOT NULL COMMENT '추가분류2',
  `ca_id3` varchar(15) NOT NULL COMMENT '추가분류3',
  `gcode` varchar(30) NOT NULL COMMENT '상품코드',
  `gname` varchar(255) NOT NULL COMMENT '상품명',
  `explan` varchar(255) NOT NULL COMMENT '짧은설명',
  `keywords` text NOT NULL COMMENT '키워드',
  `shop_state` tinyint(4) NOT NULL default '0' COMMENT '승인상태',
  `isopen` tinyint(4) NOT NULL default '1' COMMENT '판매여부',
  `normal_price` int(11) NOT NULL default '0' COMMENT '시중가격',
  `supply_price` int(11) NOT NULL default '0' COMMENT '공급가격',
  `goods_price` int(11) NOT NULL default '0' COMMENT '판매가격',
  `gpoint` int(11) NOT NULL default '0' COMMENT '적립포인트',
  `price_msg` varchar(100) NOT NULL COMMENT '가격대체문구',
  `simg_type` tinyint(4) NOT NULL default '0' COMMENT '이미지 등록방식',
  `simg1` varchar(255) NOT NULL COMMENT '이미지1',
  `simg2` varchar(255) NOT NULL COMMENT '이미지2',
  `simg3` varchar(255) NOT NULL COMMENT '이미지3',
  `simg4` varchar(255) NOT NULL COMMENT '이미지4',
  `simg5` varchar(255) NOT NULL COMMENT '이미지5',
  `simg6` varchar(255) NOT NULL COMMENT '이미지6',
  `maker` varchar(255) NOT NULL COMMENT '제조사',
  `origin` varchar(255) NOT NULL COMMENT '원산지',
  `model` varchar(255) NOT NULL COMMENT '모델명',
  `notax` tinyint(4) NOT NULL default '0' COMMENT '과세유무',
  `memo` text NOT NULL COMMENT '상세설명',
  `admin_memo` text NOT NULL COMMENT '관리자메모',
  `reg_time` datetime NOT NULL COMMENT '등록일시',
  `update_time` datetime NOT NULL COMMENT '수정일시',
  `readcount` int(11) NOT NULL default '0' COMMENT '조회수',
  `rank` int(11) NOT NULL default '0' COMMENT '노출순위',
  `sum_qty` int(11) NOT NULL default '0' COMMENT '주문합계',
  `m_count` int(11) NOT NULL default '0' COMMENT '구매후기수',
  `opt_subject` varchar(255) NOT NULL COMMENT '상품 주문옵션',
  `spl_subject` varchar(255) NOT NULL COMMENT '상품 추가옵션',
  `ppay_type` tinyint(4) NOT NULL default '0' COMMENT '판매수수료 적용타입',
  `ppay_rate` tinyint(4) NOT NULL default '0' COMMENT '판매수수료 적립유형',
  `ppay_fee` varchar(255) NOT NULL COMMENT '판매수수료 단계별금액',
  `ppay_dan` int(11) NOT NULL default '0' COMMENT '판매수수료 적립단계',
  `stock_mod` tinyint(4) NOT NULL default '0' COMMENT '수량유형',
  `stock_qty` int(11) NOT NULL default '0' COMMENT '한정수량',
  `noti_qty` int(11) NOT NULL default '0' COMMENT '통보수량',
  `brand_uid` varchar(11) NOT NULL COMMENT '브랜드주키',
  `brand_nm` varchar(255) NOT NULL COMMENT '브랜드명',
  `ec_mall_pid` varchar(255) NOT NULL COMMENT '네이버쇼핑 상품ID',
  `sc_type` tinyint(4) NOT NULL default '0' COMMENT '배송비유형',
  `sc_method` tinyint(4) NOT NULL default '0' COMMENT '배송비결제',
  `sc_minimum` int(11) NOT NULL default '0' COMMENT '조건배송비',
  `sc_amt` int(11) NOT NULL default '0' COMMENT '기본배송비',
  `sc_each_use` tinyint(4) NOT NULL default '0' COMMENT '묶음배송불가',
  `zone` varchar(30) NOT NULL COMMENT '배송가능 지역',
  `zone_msg` varchar(255) NOT NULL COMMENT '배송가능 지역 추가설명',
  `buy_level` tinyint(4) NOT NULL default '10' COMMENT '구매가능 레벨',
  `buy_only` tinyint(4) NOT NULL default '0' COMMENT '특정 레벨이상 가격공개',
  `odr_max` varchar(10) NOT NULL COMMENT '최대 주문한도',
  `odr_min` varchar(10) NOT NULL COMMENT '최소 주문한도',
  `sb_date` date NOT NULL default '0000-00-00' COMMENT '판매기간 시작일',
  `eb_date` date NOT NULL default '0000-00-00' COMMENT '판매기간 종료일',
  `info_gubun` varchar(50) NOT NULL COMMENT '정보고시 카테고리',
  `info_value` text NOT NULL COMMENT '정보고시 값',
  `use_hide` text NOT NULL COMMENT '가맹점몰 판매여부',
  `use_aff` tinyint(4) NOT NULL default '0' COMMENT '가맹점상품시(1)',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `gcode` (`gcode`),
  KEY `isopen` (`isopen`),
  KEY `shop_state` (`shop_state`),
  KEY `brand_uid` (`brand_uid`),
  KEY `use_aff` (`use_aff`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_goods_option`;
CREATE TABLE IF NOT EXISTS `shop_goods_option` (
  `io_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `io_id` varchar(255) NOT NULL default '0' COMMENT '옵션항목',
  `io_type` tinyint(4) NOT NULL default '0' COMMENT '옵션유형',
  `gs_id` varchar(20) NOT NULL COMMENT '상품주키',
  `io_price` int(11) NOT NULL default '0' COMMENT '추가금액',
  `io_stock_qty` int(11) NOT NULL default '0' COMMENT '재고수량',
  `io_noti_qty` int(11) NOT NULL default '0' COMMENT '통보수량',
  `io_use` tinyint(4) NOT NULL default '0' COMMENT '사용여부',
  PRIMARY KEY  (`io_no`),
  KEY `io_id` (`io_id`),
  KEY `gs_id` (`gs_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 옵션 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_goods_plan`;
CREATE TABLE IF NOT EXISTS `shop_goods_plan` (
  `pl_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '가맹점아이디',
  `pl_name` varchar(255) NOT NULL COMMENT '기획전명',
  `pl_it_code` text NOT NULL COMMENT '관련상품코드',
  `pl_limg` varchar(255) NOT NULL COMMENT '목록이미지',
  `pl_bimg` varchar(255) NOT NULL COMMENT '상단이미지',
  `pl_use` tinyint(4) NOT NULL default '0' COMMENT '노출여부',
  PRIMARY KEY  (`pl_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 기획전 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_goods_qa`;
CREATE TABLE IF NOT EXISTS `shop_goods_qa` (
  `iq_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(50) NOT NULL COMMENT '회원아이디',
  `seller_id` varchar(20) NOT NULL COMMENT '판매자아이디',
  `gs_id` varchar(20) NOT NULL COMMENT '상품주키',
  `iq_ty` varchar(20) NOT NULL COMMENT '구분',
  `iq_secret` tinyint(4) NOT NULL default '0' COMMENT '비밀글',
  `iq_name` varchar(50) NOT NULL COMMENT '이름',
  `iq_email` varchar(50) NOT NULL COMMENT '이메일',
  `iq_hp` varchar(30) NOT NULL COMMENT '핸드폰',
  `iq_subject` varchar(255) NOT NULL COMMENT '제목',
  `iq_question` text NOT NULL COMMENT '질문',
  `iq_answer` text NOT NULL COMMENT '답변',
  `iq_reply` tinyint(4) NOT NULL default '0' COMMENT '답변여부',
  `iq_time` datetime NOT NULL COMMENT '등록일시',
  `iq_ip` varchar(30) NOT NULL COMMENT '작성자IP',
  PRIMARY KEY  (`iq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 문의 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_goods_relation`;
CREATE TABLE IF NOT EXISTS `shop_goods_relation` (
  `gs_id` varchar(20) NOT NULL COMMENT '상품주키',
  `gs_id2` varchar(20) NOT NULL COMMENT '연관상품주키',
  `ir_no` int(11) NOT NULL default '0' COMMENT '등록순',
  PRIMARY KEY  (`gs_id`,`gs_id2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='연관 상품 테이블';


DROP TABLE IF EXISTS `shop_goods_review`;
CREATE TABLE IF NOT EXISTS `shop_goods_review` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점아이디',
  `od_index` int(11) NOT NULL COMMENT '주문 고유인덱스 주문당 리뷰작성 한번만 되도록 하기 위함',
  `od_id` varchar(30) NOT NULL COMMENT '주문번호',
  `seller_id` varchar(20) NOT NULL COMMENT '판매자아이디',
  `seller_name` varchar(100) NOT NULL COMMENT '판매자',
  `gs_id` int(11) NOT NULL default '0' COMMENT '상품주키',
  `gs_name` varchar(255) NOT NULL COMMENT '상품명',
  `gs_code` varchar(30) NOT NULL COMMENT '상품코드',
  `score` tinyint(4) NOT NULL default '0' COMMENT '평점',
  `memo` text NOT NULL COMMENT '내용',
  `reg_time` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='구매후기 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_goods_type`;
CREATE TABLE IF NOT EXISTS `shop_goods_type` (
  `gt_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(30) NOT NULL COMMENT '판매자ID',
  `gs_id` int(11) NOT NULL default '0' COMMENT '상품주키',
  `it_type1` tinyint(4) NOT NULL default '0' COMMENT '영역1',
  `it_type2` tinyint(4) NOT NULL default '0' COMMENT '영역2',
  `it_type3` tinyint(4) NOT NULL default '0' COMMENT '영역3',
  `it_type4` tinyint(4) NOT NULL default '0' COMMENT '영역4',
  `it_type5` tinyint(4) NOT NULL default '0' COMMENT '영역5',
  PRIMARY KEY  (`gt_no`),
  KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 진열관리 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_inicis_log`;
CREATE TABLE IF NOT EXISTS `shop_inicis_log` (
  `oid` bigint(20) unsigned NOT NULL,
  `P_TID` varchar(255) NOT NULL,
  `P_MID` varchar(255) NOT NULL,
  `P_AUTH_DT` varchar(255) NOT NULL,
  `P_STATUS` varchar(255) NOT NULL,
  `P_TYPE` varchar(255) NOT NULL,
  `P_OID` varchar(255) NOT NULL,
  `P_FN_NM` varchar(255) NOT NULL,
  `P_AUTH_NO` varchar(255) NOT NULL,
  `P_AMT` int(11) NOT NULL default '0',
  `P_RMESG1` varchar(255) NOT NULL,
  PRIMARY KEY  (`oid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='결제로그 테이블';


DROP TABLE IF EXISTS `shop_island`;
CREATE TABLE IF NOT EXISTS `shop_island` (
  `is_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `is_name` varchar(255) NOT NULL COMMENT '할증 지역명',
  `is_zip1` varchar(10) NOT NULL COMMENT '우편번호 시작',
  `is_zip2` varchar(10) NOT NULL COMMENT '우편번호 끝',
  `is_price` int(11) NOT NULL default '0' COMMENT '추가금액',
  PRIMARY KEY  (`is_id`),
  KEY `is_zip1` (`is_zip1`),
  KEY `is_zip2` (`is_zip2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='지역별 추가배송비 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_leave_log`;
CREATE TABLE IF NOT EXISTS `shop_leave_log` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `new_id` varchar(20) NOT NULL COMMENT '신규추천인',
  `old_id` varchar(20) NOT NULL COMMENT '기존추천인',
  `memo` text NOT NULL COMMENT '내용',
  `reg_time` datetime NOT NULL COMMENT '적용일시',
  PRIMARY KEY  (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='추천인변경 기록 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_login`;
CREATE TABLE IF NOT EXISTS `shop_login` (
  `lo_ip` varchar(255) NOT NULL COMMENT 'IP',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `lo_datetime` datetime NOT NULL COMMENT '일시',
  `lo_location` text NOT NULL COMMENT 'location name',
  `lo_url` text NOT NULL COMMENT 'location url',
  PRIMARY KEY  (`lo_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='현재접속자 테이블';


DROP TABLE IF EXISTS `shop_logo`;
CREATE TABLE IF NOT EXISTS `shop_logo` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '가맹점ID',
  `basic_logo` varchar(255) NOT NULL COMMENT '대표로고',
  `mobile_logo` varchar(255) NOT NULL COMMENT '모바일로고',
  `sns_logo` varchar(255) NOT NULL COMMENT 'SNS로고',
  `favicon_ico` varchar(255) NOT NULL COMMENT '파비콘',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 로고 테이블' AUTO_INCREMENT=2;

INSERT INTO `shop_logo` (`index_no`, `mb_id`, `basic_logo`, `mobile_logo`, `sns_logo`, `favicon_ico`) VALUES
(1, 'admin', 'banner_web.png', 'banner_mobile.png', 'sns.png', 'icon.ico');


DROP TABLE IF EXISTS `shop_mail`;
CREATE TABLE IF NOT EXISTS `shop_mail` (
  `ma_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `ma_subject` varchar(255) NOT NULL COMMENT '메일 제목',
  `ma_content` mediumtext NOT NULL COMMENT '메일 내용',
  `ma_time` datetime NOT NULL COMMENT '일시',
  `ma_ip` varchar(255) NOT NULL COMMENT 'IP',
  `ma_last_option` text NOT NULL COMMENT '메일 옵션',
  PRIMARY KEY  (`ma_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='회원 메일 관리' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_member`;
CREATE TABLE IF NOT EXISTS `shop_member` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `name` varchar(255) NOT NULL COMMENT '이름',
  `id` varchar(20) NOT NULL COMMENT '아이디',
  `sns_id` varchar(255) NOT NULL COMMENT 'sns 가입아이디',
  `pt_id` varchar(20) NOT NULL COMMENT '추천인아이디',
  `grade` tinyint(4) NOT NULL default '9' COMMENT '레벨',
  `passwd` varchar(255) NOT NULL COMMENT '비밀번호',
  `homepage` varchar(255) NOT NULL COMMENT '개별도메인',
  `theme` varchar(255) NOT NULL default 'basic' COMMENT '스킨',
  `mobile_theme` varchar(255) NOT NULL default 'basic' COMMENT '모바일스킨',
  `point` int(11) NOT NULL default '0' COMMENT '포인트잔액',
  `mb_birth` varchar(255) NOT NULL COMMENT '생년월일(8자)',
  `age` char(2) NOT NULL COMMENT '연령대',
  `gender` char(1) NOT NULL default 'M' COMMENT '성별',
  `email` varchar(255) NOT NULL COMMENT '이메일',
  `telephone` varchar(255) NOT NULL COMMENT '전화번호',
  `cellphone` varchar(255) NOT NULL COMMENT '핸드폰',
  `zip` char(5) NOT NULL COMMENT '우편번호',
  `addr1` varchar(255) NOT NULL COMMENT '기본주소',
  `addr2` varchar(255) NOT NULL COMMENT '상세주소',
  `addr3` varchar(255) NOT NULL COMMENT '참고항목',
  `addr_jibeon` varchar(255) NOT NULL COMMENT '지번',
  `mailser` char(1) NOT NULL COMMENT '이메일수신',
  `smsser` char(1) NOT NULL COMMENT '문자수신',
  `reg_time` datetime NOT NULL COMMENT '가입일시',
  `today_login` datetime NOT NULL COMMENT '최근 로그인일시',
  `term_date` date NOT NULL default '0000-00-00' COMMENT '가맹점만료일',
  `anew_date` date NOT NULL default '0000-00-00' COMMENT '가맹점등업일',
  `login_ip` varchar(100) NOT NULL COMMENT '최근 접속아이피',
  `login_sum` int(11) NOT NULL default '0' COMMENT '로그인횟수',
  `pay` int(11) NOT NULL default '0' COMMENT '가맹점수수료잔액',
  `payment` int(11) NOT NULL default '0' COMMENT '추가 판매수수료',
  `payflag` tinyint(4) NOT NULL default '0' COMMENT '추가 판매수수료 유형',
  `use_good` tinyint(4) NOT NULL default '0' COMMENT '개별 상품판매 허용',
  `use_pg` tinyint(4) NOT NULL default '0' COMMENT '개별 PG결제 허용',
  `use_app` tinyint(4) NOT NULL default '0' COMMENT '관리자 회원가입 승인',
  `memo` text NOT NULL COMMENT '메모',
  `supply` char(1) NOT NULL COMMENT '입점사여부',
  `lost_certify` varchar(255) NOT NULL COMMENT '아이디/비밀번호찾기 인증',
  `intercept_date` varchar(8) NOT NULL COMMENT '접근차단일',
  `vi_today` int(11) NOT NULL default '0' COMMENT '가맹점)오늘접속자집계',
  `vi_yesterday` int(11) default '0' COMMENT '가맹점)어제접속자집계',
  `vi_max` int(11) NOT NULL default '0' COMMENT '가맹점)최대접속자집계',
  `vi_sum` int(11) NOT NULL default '0' COMMENT '가맹점)전체접속자집계',
  `vi_history` varchar(255) NOT NULL COMMENT '접속자집계',
  `mb_certify` varchar(20) NOT NULL COMMENT '본인확인 인증',
  `mb_adult` tinyint(4) NOT NULL default '0' COMMENT '성인인증',
  `mb_dupinfo` varchar(255) NOT NULL COMMENT '중복가입 체크',
  `mb_ip` varchar(255) NOT NULL COMMENT 'IP',
  `auth_1` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한1',
  `auth_2` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한2',
  `auth_3` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한3',
  `auth_4` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한4',
  `auth_5` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한5',
  `auth_6` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한6',
  `auth_7` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한7',
  `auth_8` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한8',
  `auth_9` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한9',
  `auth_10` tinyint(4) NOT NULL default '0' COMMENT '부운영자 접근권한10',
  PRIMARY KEY  (`index_no`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='회원 테이블' AUTO_INCREMENT=2;


INSERT INTO `shop_member` (`index_no`, `name`, `id`, `sns_id`, `pt_id`, `grade`, `passwd`, `homepage`, `theme`, `mobile_theme`, `point`, `mb_birth`, `age`, `gender`, `email`, `telephone`, `cellphone`, `zip`, `addr1`, `addr2`, `addr3`, `addr_jibeon`, `mailser`, `smsser`, `reg_time`, `today_login`, `term_date`, `anew_date`, `login_ip`, `login_sum`, `pay`, `payment`, `payflag`, `use_good`, `use_pg`, `use_app`, `memo`, `supply`, `lost_certify`, `intercept_date`, `vi_today`, `vi_yesterday`, `vi_max`, `vi_sum`, `vi_history`, `mb_certify`, `mb_dupinfo`, `mb_ip`, `auth_1`, `auth_2`, `auth_3`, `auth_4`, `auth_5`, `auth_6`, `auth_7`, `auth_8`, `auth_9`, `auth_10`) VALUES
(1, '관리자', 'admin', '', '', 1, '*89C6B530AA78695E257E55D63C00A6EC9AD3E977', '', 'basic', 'basic', 0, '19750101', '40', '', 'admin@sample.com', '', '010-0000-0000', '', '', '', '', '', 'Y', 'Y', '1970-01-01 00:00:00', '1970-01-01 00:00:00', '0000-00-00', '0000-00-00', '', 0, 0, 0, 1, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '',0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


DROP TABLE IF EXISTS `shop_member_grade`;
CREATE TABLE IF NOT EXISTS `shop_member_grade` (
  `gb_no` int(11) NOT NULL auto_increment COMMENT '번호',
  `gb_name` varchar(50) NOT NULL COMMENT '레벨명',
  `gb_sale` int(11) NOT NULL default '0' COMMENT '할인률',
  `gb_sale_unit` int(11) NOT NULL default '0' COMMENT '할인률 단위절삭',
  `gb_sale_rate` tinyint(4) NOT NULL default '0' COMMENT '할인률 유형',
  `gb_anew_price` int(11) NOT NULL default '0' COMMENT '가맹점개설비',
  `gb_term_price` int(11) NOT NULL default '0' COMMENT '관리비',
  `gb_visit_pay` int(11) NOT NULL default '0' COMMENT '접속수수료(CPC)',
  `gb_promotion` int(11) NOT NULL default '0' COMMENT '자동레벨업(누적수익)',
  PRIMARY KEY  (`gb_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='회원 레벨 테이블' AUTO_INCREMENT=10;

INSERT INTO `shop_member_grade` (`gb_no`, `gb_name`, `gb_sale`, `gb_sale_unit`, `gb_sale_rate`, `gb_anew_price`, `gb_term_price`, `gb_visit_pay`, `gb_promotion`) VALUES
(1, '관리자', 0, 0, 0, 0, 0, 0, 0),
(2, '', 0, 0, 0, 0, 0, 0, 0),
(3, '', 0, 0, 0, 0, 0, 0, 0),
(4, '', 0, 0, 0, 0, 0, 0, 0),
(5, '지점', 0, 0, 0, 2000000, 20000, 2, 500000),
(6, '가맹점', 0, 0, 0, 0, 10000, 1, 0),
(7, '특별회원', 0, 0, 0, 0, 0, 0, 0),
(8, '우수회원', 0, 0, 0, 0, 0, 0, 0),
(9, '일반회원', 0, 0, 0, 0, 0, 0, 0);


DROP TABLE IF EXISTS `shop_member_leave`;
CREATE TABLE IF NOT EXISTS `shop_member_leave` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `mb_name` varchar(255) NOT NULL COMMENT '회원명',
  `memo` text NOT NULL COMMENT '메모',
  `other` text NOT NULL COMMENT '기타사항',
  `reg_time` datetime NOT NULL COMMENT '일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='회원 탈퇴기록 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE IF NOT EXISTS `shop_order` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `od_id` varchar(30) NOT NULL COMMENT '주문번호',
  `od_no` varchar(30) NOT NULL COMMENT '일련번호',
  `mb_id` varchar(20) NOT NULL COMMENT '주문자아이디',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점아이디',
  `shop_id` varchar(20) NOT NULL COMMENT '주문쇼핑몰아이디',
  `dan` tinyint(4) NOT NULL default '0' COMMENT '주문상태',
  `paymethod` varchar(255) NOT NULL COMMENT '결제방법',
  `name` varchar(255) NOT NULL COMMENT '주문자명',
  `cellphone` varchar(255) NOT NULL COMMENT '주문자 핸드폰',
  `telephone` varchar(255) NOT NULL COMMENT '주문자 전화번호',
  `email` varchar(255) NOT NULL COMMENT '주문자 이메일',
  `zip` varchar(5) NOT NULL COMMENT '주문자 우편번호',
  `addr1` varchar(255) NOT NULL COMMENT '주문자 기본주소',
  `addr2` varchar(255) NOT NULL COMMENT '주문자 상세주소',
  `addr3` varchar(255) NOT NULL COMMENT '주문자 주소 참고항목',
  `addr_jibeon` varchar(255) NOT NULL COMMENT '주문자 지번주소',
  `b_name` varchar(255) NOT NULL COMMENT '수령자명',
  `b_cellphone` varchar(255) NOT NULL COMMENT '수령자 핸드폰',
  `b_telephone` varchar(255) NOT NULL COMMENT '수령자 전화번호',
  `b_zip` varchar(5) NOT NULL COMMENT '수령자 우편번호',
  `b_addr1` varchar(255) NOT NULL COMMENT '수령자 기본주소',
  `b_addr2` varchar(255) NOT NULL COMMENT '수령자 상세주소',
  `b_addr3` varchar(255) NOT NULL COMMENT '수령자 주소 참고항목',
  `b_addr_jibeon` varchar(255) NOT NULL COMMENT '수령자 지번주소',
  `gs_id` int(11) NOT NULL default '0' COMMENT '상품주키',
  `gs_notax` tinyint(4) NOT NULL default '0' COMMENT '상품과세유무',
  `seller_id` varchar(20) NOT NULL COMMENT '판매자아이디',
  `seller_real_id` varchar(45) NOT NULL COMMENT '판매자 실제 아이디',
  `seller_name` varchar(255) NOT NULL COMMENT '판매자명',
  `sellerpay_yes` tinyint(4) NOT NULL default '0' COMMENT '입점사정산유무',
  `sum_point` int(11) NOT NULL default '0' COMMENT '총 적립포인트 ',
  `sum_qty` int(11) NOT NULL default '0' COMMENT '총 주문수량',
  `goods_price` int(11) NOT NULL default '0' COMMENT '총 상품금액',
  `supply_price` int(11) NOT NULL default '0' COMMENT '총 공급가격',
  `coupon_price` int(11) NOT NULL default '0' COMMENT '쿠폰할인금액',
  `coupon_lo_id` int(11) NOT NULL default '0' COMMENT '상품별 쿠폰 shop_coupon_log (필드:lo_id)',
  `coupon_cp_id` int(11) NOT NULL default '0' COMMENT '상품별 쿠폰 shop_coupon_log (필드:cp_id)',
  `use_price` int(11) NOT NULL default '0' COMMENT '실결제금액',
  `use_point` int(11) NOT NULL default '0' COMMENT '포인트결제',
  `baesong_price` int(11) NOT NULL default '0' COMMENT '배송비',
  `baesong_price2` int(11) NOT NULL default '0' COMMENT '추가배송비',
  `cancel_price` int(11) NOT NULL default '0' COMMENT '취소금액',
  `refund_price` int(11) NOT NULL default '0' COMMENT '환불금액',
  `bank` varchar(255) NOT NULL COMMENT '입금계좌',
  `deposit_name` varchar(255) NOT NULL COMMENT '입금자명',
  `receipt_time` datetime NOT NULL COMMENT '입금일시',
  `delivery` varchar(255) NOT NULL COMMENT '배송회사|배송추적URL',
  `delivery_no` varchar(255) NOT NULL COMMENT '운송장번호',
  `delivery_date` datetime NOT NULL COMMENT '배송일시',
  `cancel_date` datetime NOT NULL COMMENT '취소일시',
  `return_date` datetime NOT NULL COMMENT '반품완료일시',
  `change_date` datetime NOT NULL COMMENT '교환완료일시',
  `invoice_date` datetime NOT NULL COMMENT '배송완료일시',
  `refund_date` datetime NOT NULL COMMENT '환불완료일시',
  `memo` text NOT NULL COMMENT '주문시전달사항',
  `shop_memo` text NOT NULL COMMENT '관리자메모',
  `user_ok` tinyint(4) NOT NULL default '0' COMMENT '구매확정유무',
  `user_date` datetime NOT NULL COMMENT '구매확정일시',
  `taxsave_yes` char(1) NOT NULL default 'N' COMMENT '현금영수증요청',
  `taxbill_yes` char(1) NOT NULL default 'N' COMMENT '세금계산서요청',
  `company_saupja_no` varchar(255) NOT NULL COMMENT '세금계산서(사업자번호)',
  `company_name` varchar(255) NOT NULL COMMENT '세금계산서(회사명)',
  `company_owner` varchar(255) NOT NULL COMMENT '세금계산서(대표자명)',
  `company_addr` varchar(255) NOT NULL COMMENT '세금계산서(사업장주소)',
  `company_item` varchar(255) NOT NULL COMMENT '세금계산서(업태)',
  `company_service` varchar(255) NOT NULL COMMENT '세금계산서(종목)',
  `tax_hp` varchar(255) NOT NULL COMMENT '현금영수증(핸드폰)',
  `tax_saupja_no` varchar(255) NOT NULL COMMENT '현금영수증(사업자번호)',
  `od_time` datetime NOT NULL COMMENT '주문일시',
  `od_mobile` tinyint(4) NOT NULL default '0' COMMENT '모바일주문',
  `od_mod_history` text NOT NULL COMMENT '전체취소 처리 내역',
  `od_pwd` varchar(255) NOT NULL COMMENT '주문 비밀번호',
  `od_test` tinyint(4) NOT NULL default '0' COMMENT '테스트결제',
  `od_settle_pid` varchar(255) NOT NULL COMMENT 'PG계약 설정아이디',
  `od_pg` varchar(255) NOT NULL COMMENT 'PG 업체',
  `od_tno` varchar(255) NOT NULL COMMENT 'PG 거래번호',
  `od_app_no` varchar(20) NOT NULL COMMENT 'PG 승인번호',
  `od_escrow` tinyint(4) NOT NULL default '0' COMMENT '에스크로 결제',
  `od_casseqno` varchar(255) NOT NULL COMMENT '현금영수증 LG',
  `od_tax_flag` tinyint(4) NOT NULL default '0' COMMENT '복합과세결제',
  `od_tax_mny` int(11) NOT NULL default '0' COMMENT '공급원가',
  `od_vat_mny` int(11) NOT NULL default '0' COMMENT '부가세액',
  `od_free_mny` int(11) NOT NULL default '0' COMMENT '면세금액',
  `od_cash` tinyint(4) NOT NULL default '0' COMMENT '현금영수증 발급',
  `od_cash_no` varchar(255) NOT NULL COMMENT '현금영수증 번호',
  `od_cash_info` text NOT NULL COMMENT '현금영수증 내용',
  `od_goods` longtext NOT NULL COMMENT '주문시상품정보',
  `od_ip` varchar(25) NOT NULL COMMENT '주문IP',
  `review_flag` tinyint(4) NOT NULL default '1' COMMENT '리뷰작성여부 1 = 가능  0 = 불가능',
  `imp_uid` varchar(255) COMMENT '아임포트 imp_uid',
  `merchant_uid` varchar(255) COMMENT '아임포트 merchant_uid',
  PRIMARY KEY  (`index_no`),
  KEY `member` (`mb_id`),
  KEY `dan` (`dan`),
  KEY `orderdate` (`od_time`),
  KEY `od_id` (`od_id`,`od_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_order_data`;
CREATE TABLE IF NOT EXISTS `shop_order_data` (
  `od_id` bigint(20) unsigned NOT NULL COMMENT '주문번호',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `dt_pg` varchar(255) NOT NULL COMMENT '결제대행사',
  `dt_data` text NOT NULL COMMENT 'POST 값',
  `dt_time` datetime NOT NULL COMMENT '일시',
  KEY `od_id` (`od_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='결제정보 임시저장 테이블';


DROP TABLE IF EXISTS `shop_partner`;
CREATE TABLE IF NOT EXISTS `shop_partner` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `state` tinyint(4) NOT NULL default '0' COMMENT '승인여부',
  `anew_grade` tinyint(4) NOT NULL default '0' COMMENT '신청등급',
  `bank_name` varchar(255) NOT NULL COMMENT '은행명',
  `bank_account` varchar(255) NOT NULL COMMENT '계좌번호',
  `bank_holder` varchar(255) NOT NULL COMMENT '예금주명',
  `shop_name` varchar(255) NOT NULL COMMENT '쇼핑몰명',
  `shop_name_us` varchar(255) NOT NULL COMMENT '쇼핑몰 영문명',
  `saupja_yes` tinyint(4) NOT NULL default '0' COMMENT '쇼핑몰 사업자노출 여부',
  `company_type` tinyint(4) NOT NULL default '0' COMMENT '사업자유형',
  `company_name` varchar(255) NOT NULL COMMENT '회사명',
  `company_saupja_no` varchar(255) NOT NULL COMMENT '사업자등록번호',
  `tongsin_no` varchar(255) NOT NULL COMMENT '통신판매업신고번호',
  `company_tel` varchar(255) NOT NULL COMMENT '대표전화번호',
  `company_fax` varchar(255) NOT NULL COMMENT '팩스번호',
  `company_item` varchar(255) NOT NULL COMMENT '업태',
  `company_service` varchar(255) NOT NULL COMMENT '종목',
  `company_owner` varchar(255) NOT NULL COMMENT '대표자명',
  `company_zip` varchar(5) NOT NULL COMMENT '사업장우편번호',
  `company_addr` varchar(255) NOT NULL COMMENT '사업장주소',
  `company_hours` varchar(255) NOT NULL COMMENT '상담가능시간',
  `company_lunch` varchar(255) NOT NULL COMMENT '점심시간',
  `company_close` varchar(255) NOT NULL COMMENT '휴무일',
  `info_name` varchar(255) NOT NULL COMMENT '정보책임자 이름',
  `info_email` varchar(255) NOT NULL COMMENT '정보책임자 e-mail',
  `receipt_price` int(11) NOT NULL default '0' COMMENT '가맹점신청)개설비',
  `deposit_name` varchar(100) NOT NULL COMMENT '가맹점신청)입금자명',
  `pay_settle_case` tinyint(4) NOT NULL default '0' COMMENT '가맹점신청)결제방법',
  `bank_acc` varchar(50) NOT NULL COMMENT '가맹점신청)입금계좌',
  `head_title` varchar(255) NOT NULL COMMENT '브라우저 타이틀',
  `meta_author` varchar(255) NOT NULL COMMENT 'Author : 메타태그',
  `meta_description` varchar(255) NOT NULL COMMENT 'description : 메타태그',
  `meta_keywords` text NOT NULL COMMENT 'keywords : 메타태그',
  `add_meta` text NOT NULL COMMENT '추가 메타태그',
  `head_script` text NOT NULL COMMENT 'HEAD 내부 스크립트',
  `tail_script` text NOT NULL COMMENT 'BODY 내부 스크립트',
  `delivery_method` tinyint(4) NOT NULL default '1' COMMENT '배송비유형',
  `delivery_price` int(11) NOT NULL default '0' COMMENT '기본배송비',
  `delivery_price2` int(11) NOT NULL default '0' COMMENT '조건부기본배송비',
  `delivery_minimum` int(11) NOT NULL default '0' COMMENT '조건부무료배송비',
  `delivery_company` text NOT NULL COMMENT '배송업체정보',
  `baesong_cont1` text NOT NULL COMMENT '쇼핑몰 배송/교환/반품안내',
  `baesong_cont2` text NOT NULL COMMENT '모바일 배송/교환/반품안내',
  `shop_provision` text NOT NULL COMMENT '회원가입약관',
  `shop_private` text NOT NULL COMMENT '개인정보 수집 및 이용',
  `shop_policy` text NOT NULL COMMENT '개인정보처리방침',
  `de_bank_use` tinyint(4) NOT NULL default '0' COMMENT '무통장결제 사용',
  `de_card_use` tinyint(4) NOT NULL default '0' COMMENT '신용카드결제 사용',
  `de_iche_use` tinyint(4) NOT NULL default '0' COMMENT '계좌이체결제 사용',
  `de_vbank_use` tinyint(4) NOT NULL default '0' COMMENT '가상계좌결제 사용',
  `de_hp_use` tinyint(4) NOT NULL default '0' COMMENT '휴대폰결제 사용',
  `de_card_test` tinyint(4) NOT NULL default '0' COMMENT '결제 테스트',
  `de_pg_service` varchar(255) NOT NULL COMMENT '결제대행사',
  `de_tax_flag_use` tinyint(4) NOT NULL default '0' COMMENT '복합과세 결제 사용',
  `de_taxsave_use` tinyint(4) NOT NULL default '0' COMMENT '현금영수증 발급사용',
  `de_card_noint_use` tinyint(4) NOT NULL default '0' COMMENT '신용카드 무이자할부사용',
  `de_easy_pay_use` tinyint(4) NOT NULL default '0' COMMENT 'PG사 간편결제 버튼사용',
  `de_escrow_use` tinyint(4) NOT NULL default '0' COMMENT '에스크로 사용',
  `de_kcp_mid` varchar(255) NOT NULL COMMENT 'NHN KCP SITE CODE',
  `de_kcp_site_key` varchar(255) NOT NULL COMMENT 'NHN KCP SITE KEY',
  `de_inicis_mid` varchar(255) NOT NULL COMMENT 'KG이니시스 상점아이디',
  `de_inicis_admin_key` varchar(255) NOT NULL COMMENT 'KG이니시스 키패스워드',
  `de_inicis_sign_key` varchar(255) NOT NULL COMMENT 'KG이니시스 웹결제 사인키',
  `de_samsung_pay_use` tinyint(4) NOT NULL default '0' COMMENT 'KG이니시스 삼성페이 버튼 사용',
  `de_lg_mid` varchar(255) NOT NULL COMMENT 'LG유플러스 상점아이디',
  `de_lg_mert_key` varchar(255) NOT NULL COMMENT 'LG유플러스 MertKey',
  `de_kakaopay_admin_key` varchar(255) NOT NULL COMMENT '카카오페이 상점MID',
  `de_kakaopay_cid` varchar(255) NOT NULL COMMENT '카카오페이 상점키',
  `de_naverpay_mid` varchar(255) NOT NULL COMMENT '네이버페이 가맹점 아이디',
  `de_naverpay_cert_key` varchar(255) NOT NULL COMMENT '네이버페이 가맹점 인증키',
  `de_naverpay_button_key` varchar(255) NOT NULL COMMENT '네이버페이 버튼 인증키',
  `de_naverpay_test` tinyint(4) NOT NULL default '0' COMMENT '네이버페이 테스트결제',
  `de_naverpay_mb_id` varchar(255) NOT NULL COMMENT '네이버페이 결제테스트 아이디',
  `de_naverpay_sendcost` varchar(255) NOT NULL COMMENT '네이버페이 추가배송비 안내',
  `de_bank_account` text NOT NULL COMMENT '무통장입금계좌',
  `de_sns_login_use` tinyint(4) NOT NULL default '0' COMMENT '소셜네트워크 로그인 사용',
  `de_naver_appid` varchar(255) NOT NULL COMMENT '네이버 Client ID',
  `de_naver_secret` varchar(255) NOT NULL COMMENT '네이버 Client Secret',
  `de_facebook_appid` varchar(255) NOT NULL COMMENT '페이스북 앱 ID',
  `de_facebook_secret` varchar(255) NOT NULL COMMENT '페이스북 앱 Secret',
  `de_kakao_rest_apikey` varchar(255) NOT NULL COMMENT '카카오 REST API Key',
  `de_googl_shorturl_apikey` varchar(255) NOT NULL COMMENT '구글 짧은주소 API Key',
  `de_insta_url` varchar(255) NOT NULL COMMENT '인스타그램)URL',
  `de_insta_client_id` varchar(255) NOT NULL COMMENT '인스타그램)CLIENT ID',
  `de_insta_redirect_uri` varchar(255) NOT NULL COMMENT '인스타그램)Valid redirect URIs',
  `de_insta_access_token` varchar(255) NOT NULL COMMENT '인스타그램)ACCESS_TOKEN',
  `de_sns_facebook` varchar(255) NOT NULL COMMENT 'SNS URL)FACEBOOK',
  `de_sns_twitter` varchar(255) NOT NULL COMMENT 'SNS URL)TWITTER',
  `de_sns_instagram` varchar(255) NOT NULL COMMENT 'SNS URL)INSTAGRAM',
  `de_sns_pinterest` varchar(255) NOT NULL COMMENT 'SNS URL)PINTEREST',
  `de_sns_naverblog` varchar(255) NOT NULL COMMENT 'SNS URL)NAVER BLOG',
  `de_sns_naverband` varchar(255) NOT NULL COMMENT 'SNS URL)NAVER BAND',
  `de_sns_kakaotalk` varchar(255) NOT NULL COMMENT 'SNS URL)KAKAOTALK',
  `de_sns_kakaostory` varchar(255) NOT NULL COMMENT 'SNS URL)KAKAOSTORY',
  `de_maintype_title` varchar(255) NOT NULL COMMENT '카테고리별 베스트 타이틀',
  `de_maintype_best` text NOT NULL COMMENT '카테고리별 베스트 상품',
  `de_pname_use_1` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용1',
  `de_pname_use_2` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용2',
  `de_pname_use_3` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용3',
  `de_pname_use_4` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용4',
  `de_pname_use_5` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용5',
  `de_pname_use_6` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용6',
  `de_pname_use_7` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용7',
  `de_pname_use_8` tinyint(4) NOT NULL default '0' COMMENT '메뉴사용8',
  `de_pname_1` varchar(255) NOT NULL COMMENT '메뉴명1',
  `de_pname_2` varchar(255) NOT NULL COMMENT '메뉴명2',
  `de_pname_3` varchar(255) NOT NULL COMMENT '메뉴명3',
  `de_pname_4` varchar(255) NOT NULL COMMENT '메뉴명4',
  `de_pname_5` varchar(255) NOT NULL COMMENT '메뉴명5',
  `de_pname_6` varchar(255) NOT NULL COMMENT '메뉴명6',
  `de_pname_7` varchar(255) NOT NULL COMMENT '메뉴명7',
  `de_pname_8` varchar(255) NOT NULL COMMENT '메뉴명8',
  `memo` text NOT NULL COMMENT '전달사항',
  `reg_signature_json` text NOT NULL COMMENT '서명',
  `reg_ip` varchar(30) NOT NULL COMMENT 'IP',
  `reg_time` datetime NOT NULL COMMENT '신청일시',
  `update_time` datetime NOT NULL COMMENT '최근수정일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_partner_pay`;
CREATE TABLE IF NOT EXISTS `shop_partner_pay` (
  `pp_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `pp_datetime` datetime NOT NULL COMMENT '일시',
  `pp_content` varchar(255) NOT NULL COMMENT '수수료내역',
  `pp_pay` int(11) NOT NULL default '0' COMMENT '지급수수료',
  `pp_use_pay` int(11) NOT NULL default '0' COMMENT '사용수수료',
  `pp_balance` int(11) NOT NULL default '0' COMMENT '지급당시 수수료잔액',
  `pp_rel_table` varchar(20) NOT NULL COMMENT '관련 테이블',
  `pp_rel_id` varchar(20) NOT NULL COMMENT '관련 아이디',
  `pp_rel_action` varchar(255) NOT NULL COMMENT '관련 작업',
  `pp_referer` text NOT NULL COMMENT '접속 레퍼러',
  `pp_agent` varchar(255) NOT NULL COMMENT '접속 브라우저',
  PRIMARY KEY  (`pp_id`),
  KEY `index1` (`mb_id`,`pp_rel_table`,`pp_rel_id`,`pp_rel_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 수수료 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_partner_payrun`;
CREATE TABLE IF NOT EXISTS `shop_partner_payrun` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `state` tinyint(4) NOT NULL default '0' COMMENT '상태',
  `balance` int(11) NOT NULL default '0' COMMENT '출금요청액',
  `paytax` int(11) NOT NULL default '0' COMMENT '세액공제',
  `paynet` int(11) NOT NULL default '0' COMMENT '실수령액',
  `bank_name` varchar(255) NOT NULL COMMENT '은행명',
  `bank_account` varchar(255) NOT NULL COMMENT '계좌번호',
  `bank_holder` varchar(255) NOT NULL COMMENT '예금주명',
  `reg_time` datetime NOT NULL COMMENT '일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 수수료 출금요청 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_partner_term`;
CREATE TABLE IF NOT EXISTS `shop_partner_term` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `state` tinyint(4) NOT NULL default '0' COMMENT '상태',
  `expire_date` int(11) NOT NULL default '0' COMMENT '연장개월수',
  `term_price` int(11) NOT NULL default '0' COMMENT '결제금액',
  `pay_method` varchar(255) NOT NULL COMMENT '결제방법',
  `deposit_name` varchar(255) NOT NULL COMMENT '입금자명',
  `bank_account` varchar(255) NOT NULL COMMENT '본사입금계좌',
  `reg_time` datetime NOT NULL COMMENT '일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='가맹점 기간연장 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_point`;
CREATE TABLE IF NOT EXISTS `shop_point` (
  `po_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `po_datetime` datetime NOT NULL COMMENT '지급일시',
  `po_content` varchar(255) NOT NULL COMMENT '포인트내역',
  `po_point` int(11) NOT NULL default '0' COMMENT '지급포인트',
  `po_use_point` int(11) NOT NULL default '0' COMMENT '사용포인트',
  `po_mb_point` int(11) NOT NULL default '0' COMMENT '지급당시 회원포인트',
  `po_rel_table` varchar(20) NOT NULL COMMENT '관련 테이블',
  `po_rel_id` varchar(20) NOT NULL COMMENT '관련 아이디',
  `po_rel_action` varchar(255) NOT NULL COMMENT '관련 작업',
  PRIMARY KEY  (`po_id`),
  KEY `index1` (`mb_id`,`po_rel_table`,`po_rel_id`,`po_rel_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='회원 포인트 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_popular`;
CREATE TABLE IF NOT EXISTS `shop_popular` (
  `pp_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `pt_id` varchar(20) NOT NULL COMMENT '가맹점아이디',
  `pp_word` varchar(50) NOT NULL COMMENT '검색어',
  `pp_date` date NOT NULL default '0000-00-00' COMMENT '검색일',
  `pp_ip` varchar(50) NOT NULL COMMENT 'IP',
  PRIMARY KEY  (`pp_id`),
  UNIQUE KEY `index1` (`pt_id`,`pp_word`,`pp_date`,`pp_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='인기검색어 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_popup`;
CREATE TABLE IF NOT EXISTS `shop_popup` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(50) NOT NULL COMMENT '가맹점아이디',
  `state` tinyint(4) NOT NULL default '0' COMMENT '노출여부',
  `device` varchar(10) NOT NULL default 'both' COMMENT '접속기기',
  `width` int(11) NOT NULL default '0' COMMENT '팝업 가로크기',
  `height` int(11) NOT NULL default '0' COMMENT '팝업 세로크기',
  `top` int(11) NOT NULL default '0' COMMENT '팝업 상단위치',
  `lefts` int(11) NOT NULL default '0' COMMENT '팝업 좌측위치',
  `title` varchar(255) NOT NULL COMMENT '팝업 제목',
  `begin_date` date NOT NULL default '0000-00-00' COMMENT '팝업 시작일',
  `end_date` date NOT NULL default '0000-00-00' COMMENT '팝업 종료일',
  `memo` text NOT NULL COMMENT '팝업 내용',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='팝업 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_qa`;
CREATE TABLE IF NOT EXISTS `shop_qa` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `catename` varchar(100) NOT NULL COMMENT '분류',
  `subject` varchar(255) NOT NULL COMMENT '제목',
  `memo` text NOT NULL COMMENT '내용',
  `wdate` datetime NOT NULL COMMENT '등록일시',
  `result_yes` tinyint(4) NOT NULL default '0' COMMENT '답변유무',
  `result_date` datetime NOT NULL COMMENT '답변일시',
  `reply` text NOT NULL COMMENT '답변내용',
  `replyer` varchar(30) NOT NULL COMMENT '답변자',
  `email` varchar(100) NOT NULL COMMENT '작성자 이메일',
  `cellphone` varchar(30) NOT NULL COMMENT '작성자 핸드폰',
  `email_send_yes` tinyint(4) NOT NULL default '0' COMMENT '이메일 답변수신요청',
  `sms_send_yes` tinyint(4) NOT NULL default '0' COMMENT '문자 답변수신요청',
  `ip` varchar(20) NOT NULL COMMENT '작성자 IP',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='1:1 문의 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_qa_cate`;
CREATE TABLE IF NOT EXISTS `shop_qa_cate` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `catename` varchar(255) NOT NULL COMMENT '분류명',
  `isuse` char(1) NOT NULL default 'Y' COMMENT '사용여부',
  PRIMARY KEY  (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='1:1 문의 분류 테이블' AUTO_INCREMENT=9;

INSERT INTO `shop_qa_cate` (`index_no`, `catename`, `isuse`) VALUES
(1, '주문결제', 'Y'),
(2, '배송문의', 'Y'),
(3, '반품문의', 'Y'),
(4, '취소문의', 'Y'),
(5, '교환문의', 'Y'),
(6, '적립금', 'Y'),
(7, '회원관련', 'Y'),
(8, '기타문의', 'Y');


DROP TABLE IF EXISTS `shop_seller`;
CREATE TABLE IF NOT EXISTS `shop_seller` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `state` tinyint(4) NOT NULL default '0' COMMENT '승인상태',
  `seller_open` tinyint(4) NOT NULL default '1' COMMENT '전체 상품진열 상태',
  `seller_code` varchar(20) NOT NULL COMMENT '입점사 코드',
  `seller_item` varchar(255) NOT NULL COMMENT '제공상품',
  `company_name` varchar(255) NOT NULL COMMENT '입점사명',
  `company_saupja_no` varchar(255) NOT NULL COMMENT '사업자등록번호',
  `company_tel` varchar(255) NOT NULL COMMENT '대표전화번호',
  `company_fax` varchar(255) NOT NULL COMMENT '팩스번호',
  `company_item` varchar(255) NOT NULL COMMENT '업태',
  `company_service` varchar(255) NOT NULL COMMENT '종목',
  `company_owner` varchar(255) NOT NULL COMMENT '대표자명',
  `company_zip` char(5) NOT NULL COMMENT '우편번호',
  `company_addr1` varchar(255) NOT NULL COMMENT '기본주소',
  `company_addr2` varchar(255) NOT NULL COMMENT '상세주소',
  `company_addr3` varchar(255) NOT NULL COMMENT '참고항목',
  `company_addr_jibeon` varchar(255) NOT NULL COMMENT '지번',
  `company_hompage` varchar(255) NOT NULL COMMENT '홈페이지',
  `info_name` varchar(100) NOT NULL COMMENT '담당자명',
  `info_tel` varchar(100) NOT NULL COMMENT '담당자 핸드폰',
  `info_email` varchar(100) NOT NULL COMMENT '담당자 이메일',
  `bank_name` varchar(100) NOT NULL COMMENT '은행명',
  `bank_account` varchar(100) NOT NULL COMMENT '계좌번호',
  `bank_holder` varchar(100) NOT NULL COMMENT '예금주명',
  `delivery_method` tinyint(4) NOT NULL default '1' COMMENT '배송비유형',
  `delivery_price` int(11) NOT NULL default '0' COMMENT '기본배송비',
  `delivery_price2` int(11) NOT NULL default '0' COMMENT '조건부 기본배송비',
  `delivery_minimum` int(11) NOT NULL default '0' COMMENT '조건부무료배송비',
  `delivery_company` text NOT NULL COMMENT '배송업체정보',
  `baesong_cont1` text NOT NULL COMMENT '쇼핑몰 배송/교환/반품안내',
  `baesong_cont2` text NOT NULL COMMENT '모바일 배송/교환/반품안내',
  `memo` text NOT NULL COMMENT '전달사항',
  `reg_time` datetime NOT NULL COMMENT '신청일시',
  `update_time` datetime NOT NULL COMMENT '수정일시',
  PRIMARY KEY  (`index_no`),
  KEY `mb_id` (`mb_id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='입점사 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_seller_cal`;
CREATE TABLE IF NOT EXISTS `shop_seller_cal` (
  `index_no` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '아이디',
  `order_idx` text NOT NULL COMMENT '주문서주키',
  `tot_price` int(111) NOT NULL default '0' COMMENT '주문금액',
  `tot_point` int(11) NOT NULL default '0' COMMENT '포인트결제',
  `tot_coupon` int(11) NOT NULL default '0' COMMENT '쿠폰할인',
  `tot_baesong` int(11) NOT NULL default '0' COMMENT '배송비',
  `tot_supply` int(11) NOT NULL default '0' COMMENT '공급가총액',
  `tot_seller` int(11) NOT NULL default '0' COMMENT '실정산액',
  `tot_partner` int(11) NOT NULL default '0' COMMENT '가맹점수수료',
  `tot_admin` int(11) NOT NULL default '0' COMMENT '본사마진',
  `reg_time` datetime NOT NULL COMMENT '처리일시',
  PRIMARY KEY  (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='입점사 정산내역 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_sms`;
CREATE TABLE IF NOT EXISTS `shop_sms` (
  `mb_id` varchar(20) NOT NULL COMMENT '가맹점아이디',
  `cf_sms_use` tinyint(4) NOT NULL default '0' COMMENT 'SMS 사용',
  `cf_sms_type` varchar(255) NOT NULL COMMENT 'SMS 전송유형',
  `cf_sms_recall` varchar(255) NOT NULL COMMENT '발신 전화번호',
  `cf_moonjaro_server_ip` varchar(255) NOT NULL COMMENT '문자로 아이피',
  `cf_moonjaro_server_port` varchar(255) NOT NULL COMMENT '문자로 포트번호',
  `cf_moonjaro_id` varchar(255) NOT NULL COMMENT '문자로 아이디',
  `cf_moonjaro_pw` varchar(255) NOT NULL COMMENT '문자로 비밀번호',
  `cf_moonjaro_key` varchar(255) NOT NULL default '' COMMENT '문자로 인증키',
  `cf_moonjaro_location` varchar(255) NOT NULL default '' COMMENT '문자로 api요청 주소',
  `cf_cont1` text NOT NULL COMMENT '회원가입 메세지',
  `cf_cont2` text NOT NULL COMMENT '주문완료 메세지',
  `cf_cont3` text NOT NULL COMMENT '입금확인 메세지',
  `cf_cont4` text NOT NULL COMMENT '상품발주 메세지',
  `cf_cont5` text NOT NULL COMMENT '주문취소 메세지',
  `cf_cont6` text NOT NULL COMMENT '배송완료 메세지',
  `cf_mb_use1` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 고객전송',
  `cf_ad_use1` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 관리자전송',
  `cf_re_use1` tinyint(4) NOT NULL default '0' COMMENT '회원가입시 가맹점전송',
  `cf_sr_use1` tinyint(4) NOT NULL default '0' COMMENT '미사용',
  `cf_mb_use2` tinyint(4) NOT NULL default '0' COMMENT '주문완료시 고객전송',
  `cf_ad_use2` tinyint(4) NOT NULL default '0' COMMENT '주문완료시 관리자전송',
  `cf_re_use2` tinyint(4) NOT NULL default '0' COMMENT '주문완료시 가맹점전송',
  `cf_sr_use2` tinyint(4) NOT NULL default '0' COMMENT '주문완료시 입점사전송',
  `cf_mb_use3` tinyint(4) NOT NULL default '0' COMMENT '입금확인시 고객전송',
  `cf_ad_use3` tinyint(4) NOT NULL default '0' COMMENT '입금확인시 관리자전송',
  `cf_re_use3` tinyint(4) NOT NULL default '0' COMMENT '입금확인시 가맹점전송',
  `cf_sr_use3` tinyint(4) NOT NULL default '0' COMMENT '입금확인시 입점사전송',
  `cf_mb_use4` tinyint(4) NOT NULL default '0' COMMENT '상품발주시 고객전송',
  `cf_ad_use4` tinyint(4) NOT NULL default '0' COMMENT '상품발주시 관리자전송',
  `cf_re_use4` tinyint(4) NOT NULL default '0' COMMENT '상품발주시 가맹점전송',
  `cf_sr_use4` tinyint(4) NOT NULL default '0' COMMENT '상품발주시 입점사전송',
  `cf_mb_use5` tinyint(4) NOT NULL default '0' COMMENT '주문취소시 고객전송',
  `cf_ad_use5` tinyint(4) NOT NULL default '0' COMMENT '주문취소시 관리자전송',
  `cf_re_use5` tinyint(4) NOT NULL default '0' COMMENT '주문취소시 가맹점전송',
  `cf_sr_use5` tinyint(4) NOT NULL default '0' COMMENT '주문취소시 입점사전송',
  `cf_mb_use6` tinyint(4) NOT NULL default '0' COMMENT '배송완료시 고객전송',
  `cf_ad_use6` tinyint(4) NOT NULL default '0' COMMENT '배송완료시 관리자전송',
  `cf_re_use6` tinyint(4) NOT NULL default '0' COMMENT '배송완료시 가맹점전송',
  `cf_sr_use6` tinyint(4) NOT NULL default '0' COMMENT '배송완료시 입점사전송',
  UNIQUE KEY `mb_id` (`mb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SMS 테이블';

INSERT INTO `shop_sms` (`mb_id`, `cf_sms_use`, `cf_sms_type`, `cf_sms_recall`, `cf_moonjaro_server_ip`, `cf_moonjaro_server_port`, `cf_moonjaro_id`, `cf_moonjaro_pw`, `cf_moonjaro_location`, `cf_cont1`, `cf_cont2`, `cf_cont3`, `cf_cont4`, `cf_cont5`, `cf_cont6`, `cf_mb_use1`, `cf_ad_use1`, `cf_re_use1`, `cf_sr_use1`, `cf_mb_use2`, `cf_ad_use2`, `cf_re_use2`, `cf_sr_use2`, `cf_mb_use3`, `cf_ad_use3`, `cf_re_use3`, `cf_sr_use3`, `cf_mb_use4`, `cf_ad_use4`, `cf_re_use4`, `cf_sr_use4`, `cf_mb_use5`, `cf_ad_use5`, `cf_re_use5`, `cf_sr_use5`, `cf_mb_use6`, `cf_ad_use6`, `cf_re_use6`, `cf_sr_use6`) VALUES
('admin', 0, 'LMS', '1600-0000', '', '', '', '', 'http://oapi.moonjaro.co.kr/sms/send', '{이름}님 회원가입을 축하합니다. 가입하신 아디디는 {아이디}입니다.', '{이름}님 {주문번호} 주문이 정상적으로 주문완료 되었습니다.', '{이름}님 {주문번호} 주문이 입금 확인되었습니다.', '{이름}님 {주문번호} 상품이 발송 되었습니다. 배송업체:{업체}, 송장번호:{송장번호}', '{이름}님 {주문번호} 상품이 취소 되었습니다.', '{이름}님 {주문번호} 상품이 배송완료 되었습니다.', 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 0, 0);


DROP TABLE IF EXISTS `shop_uniqid`;
CREATE TABLE IF NOT EXISTS `shop_uniqid` (
  `uq_id` bigint(20) unsigned NOT NULL COMMENT '유니크키',
  `uq_ip` varchar(255) NOT NULL COMMENT '생성IP',
  PRIMARY KEY  (`uq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='유니크키 테이블';


DROP TABLE IF EXISTS `shop_visit`;
CREATE TABLE IF NOT EXISTS `shop_visit` (
  `vi_id` int(11) NOT NULL default '0' COMMENT '주키',
  `mb_id` varchar(30) NOT NULL COMMENT '가맹점아이디',
  `vi_ip` varchar(255) NOT NULL COMMENT '접속IP',
  `vi_date` date NOT NULL default '0000-00-00' COMMENT '접속일',
  `vi_time` time NOT NULL default '00:00:00' COMMENT '접속시간',
  `vi_referer` text NOT NULL COMMENT '접속 레퍼러',
  `vi_agent` varchar(255) NOT NULL COMMENT '접속 브라우저',
  PRIMARY KEY  (`vi_id`),
  UNIQUE KEY `index1` (`vi_ip`,`vi_date`),
  KEY `index2` (`vi_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속자 테이블';


DROP TABLE IF EXISTS `shop_visit_sum`;
CREATE TABLE IF NOT EXISTS `shop_visit_sum` (
  `vs_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(30) NOT NULL COMMENT '가맹점아이디',
  `vs_date` date NOT NULL default '0000-00-00' COMMENT '방문일',
  `vs_count` int(11) NOT NULL default '0' COMMENT '방문자수',
  PRIMARY KEY  (`vs_id`),
  KEY `index1` (`vs_count`),
  KEY `vs_date` (`vs_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='접속자 합계 테이블' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `shop_wish`;
CREATE TABLE IF NOT EXISTS `shop_wish` (
  `wi_id` int(11) NOT NULL auto_increment COMMENT '주키',
  `mb_id` varchar(20) NOT NULL COMMENT '회원아이디',
  `gs_id` int(11) NOT NULL default '0' COMMENT '상품주키',
  `wi_time` datetime NOT NULL COMMENT '일시',
  `wi_ip` varchar(25) NOT NULL COMMENT '회원IP',
  PRIMARY KEY  (`wi_id`),
  KEY `mb_id` (`mb_id`,`gs_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품 찜 테이블' AUTO_INCREMENT=1;