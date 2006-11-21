CREATE TABLE IF NOT EXISTS announcement ( 
	announcement_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY , 
	description VARCHAR( 50 ) , 
	recording VARCHAR( 255 ) , 
	allow_skip INT,
	post_dest VARCHAR( 255 ) 
);
