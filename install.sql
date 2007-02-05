CREATE TABLE IF NOT EXISTS announcement ( 
	announcement_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY , 
	description VARCHAR( 50 ) , 
	recording VARCHAR( 255 ) , 
	allow_skip INT,
	post_dest VARCHAR( 255 ) ,
	return_ivr TINYINT(1) NOT NULL DEFAULT 0,
	noanswer TINYINT(1) NOT NULL DEFAULT 0
);
