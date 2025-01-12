[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/5k4uDUDX)

# Project structure

## Idea - 
My idea is based on a simple list of tasks that are saved in a database and upon starting a log-in session the user can add tasks to their user account.
They will be able to Mark the task as Complete, amend the task and save it, delete the task from their user account and also create a new task saved only to themselves.

It will be Harry potter themed and include some Christmas themed tasks.

## Plan - 
1. ER Diagram
2. Create simple Figma design
3. Create file structure in VS Code
4. Build database in MariaDB
5. Design a simple index.php using HTML and CSS
6. Build SQL questions with PDO
7. Create connection to index page with backend database
8. Fine tune styling
9. TEST
10. Feature expansion (if time allows)

## Execution - 
From week 48 - week 52 as outlined in assignment
1. Design and style index page
2. Create and connect database
3. Implement CRUD functions -
    - Create
    - Read
    - Update
    - Delete

## Production ready - 
Week 1 - 2. Application to be in a working function, with maybe some fine tuning to do.

## Retro Perspective - 
Week 2 -
Look back at the project: What went well? What went bad? How can I do it differently in my next project?
I feel that my project is structured quite well and all the functions work as intended. I would like to have restructed 
the code into OOP for a cleaner and easier to read format.
Feature expansion - any other desired features to implement?
Currently the amend task function allows the user to update the intended task successfully, however it updated the task into the 
main Task table database which causes other users to also see this change. 
I would have liked to work on this function so that amendments made to a task would created a dupplicate amended version saved only to the user
so that the original task is left untouched and available to other users upon log-in.

Link to Figma for ER-diagram 
https://www.figma.com/design/IbF5MVSAe7AfwUPpZbyZ8M/ER-Diagram-To-Do-List?node-id=0-1&p=f&t=9EC8nk7DR4qQpT81-0

Link to Figma for site design 
https://www.figma.com/design/Ph6qDZ8Bf439eGvyIXNtIB/Harry-Potter-To-Do-App-Design?node-id=0-1&p=f&t=HUR9FOUHrtALDEhQ-0
