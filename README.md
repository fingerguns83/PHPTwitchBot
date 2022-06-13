#PHPTwitchBot

I had an old repo based on this same code, but my local code got so far ahead and I couldn't get it to merge correctly so I just deleted that repo and made a new one.

This is a chat bot for Twitch streams written in PHP. As such, you can run it on your own machine regardless of platform (maybe not Windows? But there are enough streaming tools for Windows anyways.)

The only dependancy not included here is MySQL.

##Setup
1. Edit setup_db.php with your MySQL host, username, and password
2. Run ```php setup_db.php```
3. Register a Twitch application at https://dev.twitch.tv/console
3. Copy secret.example.php to secret.php and edit the values.

##Configuration
- To add a new command, you'll have to add it to the database. For commands with a static output, simply add the static output to the "output" column for that command. If you wish to have it run a function, write "function" to that column and then create a .php file in the "functions" folder with the same name as the command (case-sensitive).
- To disable a command, change the commands "turned_off" value to 1 in the database.
- To delete a command, simply remove its database entry
- By default, the bot will only work while the stream is live (unless you interact with it from the channel you set as "broadcaster" in secret.php). To disable this behavior so the bot runs all the time (increasing responsiveness, but could result in unintended interactions), uncomment line 116 of reqs.php.

##Run
To run the bot, simply run ```php main.php```. (I set mine up with systemctl for unattended access. I find it works nicely. For more info, read this: https://www.digitalocean.com/community/tutorials/how-to-use-systemctl-to-manage-systemd-services-and-units)

##To-Do
- Command management via CLI or GUI.
- Front-end "Browser Source" for toasts and sound alerts
- Channel points integration
- Follow/Subscribe/Cheer notifications