CREATE TABLE students (

id INT AUTO_INCREMENT PRIMARY KEY,

first_name VARCHAR(100) NOT NULL,

last_name VARCHAR(100) NOT NULL,

father_name VARCHAR(150) NOT NULL,

email VARCHAR(150) UNIQUE NOT NULL,

mobile VARCHAR(20) UNIQUE NOT NULL,

cnic VARCHAR(20) UNIQUE NOT NULL,

password VARCHAR(255) NOT NULL,

status ENUM('Active','Inactive') DEFAULT 'Active',

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);
/*==================================================
FACULTIES
==================================================*/

CREATE TABLE faculties(

id INT AUTO_INCREMENT PRIMARY KEY,

faculty_name VARCHAR(150) NOT NULL,

status ENUM('Active','Inactive') DEFAULT 'Active'

);


/*==================================================
PROGRAMS
==================================================*/

CREATE TABLE programs(

id INT AUTO_INCREMENT PRIMARY KEY,

faculty_id INT NOT NULL,

program_name VARCHAR(150) NOT NULL,

program_code VARCHAR(20),

duration VARCHAR(30),

status ENUM('Active','Inactive') DEFAULT 'Active',

FOREIGN KEY (faculty_id) REFERENCES faculties(id)

);


/*==================================================
APPLICATIONS
==================================================*/

CREATE TABLE applications(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id VARCHAR(30) UNIQUE,

student_id INT NOT NULL,

faculty_id INT NOT NULL,

program_id INT NOT NULL,

application_status ENUM(
'Draft',
'Submitted',
'Under Review',
'Selected',
'Rejected'
) DEFAULT 'Draft',

merit_marks DECIMAL(6,2) DEFAULT 0,

shift ENUM(
'Morning',
'Evening'
) DEFAULT NULL,

registration_no VARCHAR(50),

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(student_id) REFERENCES students(id),

FOREIGN KEY(faculty_id) REFERENCES faculties(id),

FOREIGN KEY(program_id) REFERENCES programs(id)

);
/*==================================================
APPLICATION PERSONAL INFORMATION
==================================================*/

CREATE TABLE application_personal(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id INT,

gender VARCHAR(20),

date_of_birth DATE,

religion VARCHAR(50),

nationality VARCHAR(50),

address TEXT,

city VARCHAR(100),

province VARCHAR(100),

postal_code VARCHAR(20),

FOREIGN KEY(application_id) REFERENCES applications(id)

);


/*==================================================
APPLICATION ACADEMIC INFORMATION
==================================================*/

CREATE TABLE application_academic(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id INT,

matric_board VARCHAR(150),

matric_roll_no VARCHAR(50),

matric_total_marks INT,

matric_obtained_marks INT,

inter_board VARCHAR(150),

inter_roll_no VARCHAR(50),

inter_total_marks INT,

inter_obtained_marks INT,

FOREIGN KEY(application_id) REFERENCES applications(id)

);
/*==================================================
APPLICATION DOCUMENTS
==================================================*/

CREATE TABLE application_documents(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id INT,

student_photo VARCHAR(255),

student_cnic VARCHAR(255),

father_cnic VARCHAR(255),

matric_result_card VARCHAR(255),

inter_result_card VARCHAR(255),

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(application_id) REFERENCES applications(id)

);
/*==================================================
DEFAULT FACULTIES
==================================================*/

INSERT INTO faculties(faculty_name)

VALUES

('Faculty of Computing'),

('Faculty of Business & Management'),

('Faculty of Engineering'),

('Faculty of Science'),

('Faculty of Arts & Social Sciences');


/*==================================================
DEFAULT PROGRAMS
==================================================*/

INSERT INTO programs
(faculty_id,program_name,program_code,duration)

VALUES

(1,'BS Computer Science','BSCS','4 Years'),

(1,'BS Software Engineering','BSSE','4 Years'),

(1,'BS Information Technology','BSIT','4 Years'),

(1,'BS Cyber Security','BSCSY','4 Years'),

(2,'BBA','BBA','4 Years'),

(2,'BS Accounting & Finance','BSAF','4 Years'),

(3,'BS Civil Engineering','BSCE','4 Years'),

(3,'BS Electrical Engineering','BSEE','4 Years'),

(3,'BS Mechanical Engineering','BSME','4 Years'),

(4,'BS Mathematics','BSMATH','4 Years'),

(4,'BS Physics','BSPHY','4 Years'),

(4,'BS Chemistry','BSCHEM','4 Years'),

(5,'BS English','BSENG','4 Years'),

(5,'BS Psychology','BSPSY','4 Years');

/*==================================================
APPLICATION PAYMENT
==================================================*/

CREATE TABLE application_payments(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id INT NOT NULL,

challan_no VARCHAR(50) UNIQUE,

processing_fee DECIMAL(10,2) DEFAULT 1050.00,

payment_status ENUM(
'Pending',
'Paid',
'Expired'
) DEFAULT 'Pending',

bank_name VARCHAR(100),

payment_date DATE,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(application_id) REFERENCES applications(id)

);
/*==================================================
MERIT LIST
==================================================*/

CREATE TABLE merit_lists(

id INT AUTO_INCREMENT PRIMARY KEY,

application_id INT NOT NULL,

merit_no INT,

merit_marks DECIMAL(6,2),

selected_shift ENUM(
'Morning',
'Evening'
),

registration_no VARCHAR(50),

status ENUM(
'Selected',
'Waiting',
'Rejected'
) DEFAULT 'Waiting',

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(application_id) REFERENCES applications(id)

);