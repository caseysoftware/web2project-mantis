W2P-mantis 0.9

How to install this Integration between web2Project and Mantis ?

Versions tested:
Web2Project 1.2
Mantis 1.2.0

1. Copy the contents of this package into the root of your Web2Project installation.
There are no files being overwritten.

2. Copy docs/mantis/index_dp.php into the root of your mantis installation
3. Copy the logo from modules/mantis/images to style/web2project/images

4. Add the contents of docs/mantis/config.txt to includes/config.php (inside your Web2Project installation)
5. Now adjust those settings to your environment, the settings are described in config.txt
6. In case you decide for Custom field to define Mantis projects, read the secition below first.
7. Go !!!!!!!!!

How does it work ?
Within W2P, you will find a Tab called Mantis when looking at Projects.
For each W2P project, a Mantis project will be created when the first issue is added.

Creation of Projects in mantis can be automatic or by using a custom field within Web2Project

In case defined as automatic, Projects in Mantis will be created with a special name :
1. Prefix as defined in config.php
2. W2P-project-id
3. dot
4. W2P-task-id
5. Space
6. W2P-project-name

In case defined as Custom, Projects in Mantis will be created as follows :
1. First of all, a one time action, a custom field needs to be created in W2P on project level.
2. This field needs to be called "Mantis" and type should be TEXT.

Now you have an additional field in your project definition where you can define the projectname for Mantis.
If the system is configured to "Custom" and no Mantis name is provided, the system will assume the same name for the project within W2P and mantis.
Be aware that the setting is NOT case sensitive.

If the system is configured to "Custom", there is no use in activating mantis in the top menu bar, it will generate no results. 
In that case, only results can be expected when selecting the corresponding project tab.

On the tab all issues related to the project will be shown with the following information:
1. Issue-id in Mantis
2. Date-Time reported
3. Reporter of issue
4. Status
5. Summary
6. Description
The summary actually is a hyperlink which takes you straight into Mantis to maintain the issue.

There is also a button which allows you to add an issue to the project. All you need to fill in is the Summary and the description. Upon saving the issue is stored in Mantis.
The current W2P-user is checked by username in Mantis. If this person does not exist, a user record is created in Mantis.
An email for direct access to Mantis with the password will be send to this user (if configured).
The current project is checked and if it does not yet exists, it will be created.
For the issue the following items are recorded :
1. Project-id
2. Reporter-id
3. Summary
4. Description
5. Date-submitted
6. Date-last-updated
All other activity is left within Mantis.


ToDo:
1. Verifying other password encryptions than just PLAIN
