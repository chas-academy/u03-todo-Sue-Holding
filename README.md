[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/5k4uDUDX)

# Sue Holding's To Do Application

## 1. Idea and Instructions on how to use the application

My idea is based on a simple list of tasks that are saved in a database and upon starting a log-in session the user can add tasks to their user account.
The user will be able to mark the task as complete, amend the task and save it, delete the task from their user account and also create a new task saved only to themselves.
It will be Harry potter themed and include some Christmas themed tasks.
    - Build Dockerfile and docker-compose.yml from the project which has the access to Adminer installed
    - To access the database on MariaDB use the following;
            servername = mariabd
            username = root
            password = mariadb
            dbname = harry_potter_todo_app

## 2. SQL Code for Database Setup

The database is created with 3 tables; Tasks, user_data and user_tasks. The user_tasks table acts as a pivots table and links the UserID and TaskID to a many-to-many relationship.
Here are the SQL commands to create each table.

CREATE TABLE Tasks (
    TaskID INT(11) NOT NULL AUTO_INCREMENT,
    Category VARCHAR(100) NOT NULL,
    House VARCHAR(100) NOT NULL,
    TaskType VARCHAR(100) NOT NULL,
    Description TEXT NULL,
    DueDate DATETIME NULL,
    Status ENUM('Not Completed', 'Completed') DEFAULT 'Not Completed',
    Daily ENUM('Yes', 'No') NULL,
    Christmas ENUM('Yes', 'No') NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CreatedBy INT(11) DEFAULT 0,
    PRIMARY KEY (TaskID)
);

CREATE TABLE user_data (
    UserID INT(11) NOT NULL AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (UserID),
    UNIQUE KEY (Username)
);

CREATE TABLE user_tasks (
    UserID INT(11) NOT NULL,
    TaskID INT(11) NOT NULL,
    Status ENUM('Not Completed', 'Completed') NULL DEFAULT 'Not Completed',
    PRIMARY KEY (UserID, TaskID),
    INDEX (TaskID),
    CONSTRAINT fk_user FOREIGN KEY (UserID) REFERENCES user_data(UserID) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_task FOREIGN KEY (TaskID) REFERENCES Tasks(TaskID) ON DELETE RESTRICT ON UPDATE RESTRICT
);

## 3. Figma links to design and ER Diagram -

Here are the links to my design sketch and ER diagram in Figma. They are also saved as image attachments to the project.
Link to Figma for ER-diagram
<https://www.figma.com/design/IbF5MVSAe7AfwUPpZbyZ8M/ER-Diagram-To-Do-List?node-id=0-1&p=f&t=9EC8nk7DR4qQpT81-0>

Link to Figma for site design
<https://www.figma.com/design/Ph6qDZ8Bf439eGvyIXNtIB/Harry-Potter-To-Do-App-Design?node-id=0-1&p=f&t=HUR9FOUHrtALDEhQ-0>

## 4. Retro Perspective

I feel that my project is structured quite well and all the functions work as intended. I would like to have restructed
the code into OOP for a cleaner and easier to read format.
Currently the amend task function allows the user to update the intended task successfully, however it updated the task into the
main Task table database which causes other users to also see this change.
I would have liked to work on this function so that amendments made to a task would created a dupplicate amended version saved only to the user so that the original task is left untouched and available to other users upon log-in.

I have also corrected my code to the PSR-12 standards but have a warning that I am unable to solve.

FILE: /var/www/html/src/functions/crud.php
FILE: /var/www/html/src/views/submit.php
-------------------------------------------------------------------------------------------------------------------------------------------| WARNING | A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 8 and the first side effect is on line 3.
