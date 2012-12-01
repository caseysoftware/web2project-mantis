web2project-mantis
==================

(This content is lifted from the web2project forums and is provided here without comment or correction for background purposes.)

Source: http://forums.web2project.net/viewtopic.php?p=4808#p4808

Web2Project Mantis Addon

Module Overview
This module provides a way for a user to raise issues within Mantis in relation to a W2P projec

How does it work ?
Within W2P, you will find a Tab called Mantis when looking at a given project.
For each W2P project, a Mantis project will be created (if it does not yet exist) when the first issue is added.

Creation of Projects in mantis can be automatic or by using a custom field within Web2Project
In case defined as automatic, Projects in Mantis will be created with a special name :
1. Prefix as defined in config.php
2. W2P-project-id
3. dot
4. W2P-task-id
5. Space
6. W2P-project-name
In case defined as Custom, Projects in Mantis will be created as follows :
Bsed upon a Custom field such that the name can begiven freely:
1. First of all, a one time action, a custom field needs to be created in W2P on project level.
2. This field needs to be called "Mantis" and type should be TEXT.
Now you have an additional field in your project definition where you can define the projectname for Mantis.
Based on vanilla W2P
1. Project name in mantis will be identical to the projectname in W2P.
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

This module is available for download here: http://web2project.net/forums/viewtopic.php?t=1843

Release Notes
V0.9 30-03-2010
Initial release, converted from DP.
In addition this version is only suitable for Mantis version 1.2.0 and above.