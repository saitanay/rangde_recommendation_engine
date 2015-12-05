#Rang De Recommendation Engine

#About
The engine provides a simple HTTP API that can be called from any system, with Investor Id and Number of Recommendations needed as inputs. The engine returns recommended Loan Profile Ids. 


#Installation
The engine can be setup as a separate system outside of the current J2EE stack. 
1. On any simple PHP Stack, copy the folder with these 3 files - cron.php, recommendation.php and connection.php
2. Edit connection.php with the details required to connect to the MySql DB of rangde portal (host, mysql username, password and dbname)
3. Setup a cron job to run "cron.php" every 3 hours

You are all set now :-)

#Usage
Assuming you have setup the engine available at http://rangde.com/rangde_recommendation_engine , from the existing system, you can make a simple HTTP call to http://rangde.com/rangde_recommendation_engine with the following GET params - 
* inv_id (Investor User id that you want recommendations for) 
* n (Number of recommendations required)


#Example call:
`http://rangde.com/rangde_recommendation_engine/recommend.php?inv_id=995&n=3`

`http://localhost/rangde/recommend.php?inv_id=995&n=3`

Sample Output:
`["150075","169177","230999"]`

#Recommendation Logic
The engine tries to solve the problem of "ignored states" and at the same time tries to leverage the fact that most investors tend to invest in their home state. The engine generates and stores demographic data about percentage of funding achieved for each state and the top and bottom loan profiles in each state. 

For any investor, if the investor belongs to a state that is in the BOTTOM 50% of the states by investment, he is recommended the least-funded loan profiles from his own states. 

For any investor, if the investor belongs to a state that is in the TOP 50% of the states by investment, he is recommended N least-funded loan profiles, one each from the least-funded states. 

#Todo
Should Rangede choose to implement this recommendation engine, some exceptions should be handled - Like handling when the given user id doesn't have a state mentioned on the profile etc. The code could be better commented and formatted and cleaned. 







