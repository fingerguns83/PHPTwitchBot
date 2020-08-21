# PHPTwitchBot
A Twitch Chat bot written in PHP using [ReactPHP](https://reactphp.org)


## Installation/Configuration
1. Run 'composer install' to grab the necessary dependancies
2. Add the required "secret" and "nick" values to 'twitchclient.php'
3. Customize your commands and responses in 'responses.php'


## To-Do
- There could be more comments, it's kind of a mess
- 'twitchfunctions.php' could use a good cleanup refactor
- I couldn't get it to work with the SSL socket (6697), that should probably get resolved


## Road Map
- Implement some sort of argument system?
- Integrate Twitch API for things like viewer count, uptime, mod tools, etc.


## Wishful thinking
Whilst on my ReactPHP kick, I'm considering using [Zenity](https://github.com/clue/reactphp-zenity) to try making a nice GUI for editing the command list (which could be expanded for mod lists/tools later). We'll see how I'm feeling.
