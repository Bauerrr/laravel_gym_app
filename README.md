# Gym App
## Table of contents
* [General info](#general-info)
* [Setup](#setup)
* [Backend info](#backend-info)
* [Frontend info](#frontend-info)

## General info
The application allows you to track progress at the gym by calculating scores for each day based on exercises. 
To do so, it utilizes a backend API written in Laravel and a frontend written in Angular.

## Setup
TODO

## Backend info

### Used technologies
`Laravel`: Backend framework  
`Docker`: Containarization for PHP, Node.js, queue, MySQL, Redis  
`PHPUnit`: Testing  
`MySQL`: Database  
`Redis`: Cache for database  

### User Authentication Routes
#### Signup
`POST`: api/signup  
Create new user account.

#### Login
`POST`: api/login  
Login to an already existing account.

#### Logout
`POST`: api/logout
Logout from logged in account.

### Exercise Routes
Those routes reqire user to be logged in as they receive, create and delete information only for a logged in user.

#### Get all exercises
`GET`: api/exercises  
Get all exercises.

#### Create new exercise
`POST`: api/exercises  
Create new exercise.

#### Get exercise by id
`GET`: api/exercises/{exercise}  
Receive all information about given exercise.

#### Edit exercise by id
`PUT` or 'PATCH': api/exercises/{exercise}  
Update exercise information.

#### Delete exercise by id
`DELETE`: api/exercises/{exercise}  
Delete exercise.

### Day Routes
Those routes reqire user to be logged in as they receive, create and delete information only for a logged in user.

#### Get all exercises for a given day
`GET`: api/days/{name}  
Get all exercises attached to a given day for a given user.

#### Add exercise to a given day
`POST`: api/days/{name}  
Attach exercise to day.

#### Detach exercise from a given day
`DELETE`: api/days/{name}  
Detach exercise from day.

### Score Routes
Those routes reqire user to be logged in as they receive, create and delete information only for a logged in user.

#### Create or update score for a given day
`POST`: api/scores/{day_name}  
Calculate score for a given day.

#### Get score for a given day
`GET`: api/scores/{day_name}  
Get score for a given day.

## Fronted info
TODO