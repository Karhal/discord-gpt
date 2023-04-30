# Discord GPT Bot

To create a Discord GPT bot.

## Installation

To install the bot, you need to follow these steps:

1. Clone this repository on your local machine: `git clone https://github.com/karhal/discord-gpt.git`.
2. Navigate to the cloned repository folder: `cd discord-gpt`.
3. Install the project dependencies by running: `composer install`.
4. Make sure you have PHP version > 7.4 installed on your machine.
5. Obtain an API key from OpenAI. You can sign up and get a free key [here](https://beta.openai.com/signup/).
6. Obtain a Discord bot token. You can create a bot and get a token by following the instructions [here](https://discordpy.readthedocs.io/en/stable/discord.html).
7. copy/paste the `config.override.yaml.dist` file into `config.override.yaml`.
8. Edit the `config.override.yaml` file to personalize the bot settings (see the "Configuration" section below).

## Configuration

The `config.override.yaml` file contains the configuration settings for the bot. You can personalize the following parameters:

### `discord.token`

Your Discord bot token. This token is required for the bot to connect to Discord.

### `openai.key`

Your OpenAI API key. This key is required for the bot to interact with OpenAI's GPT models.

### `openai.config.model`

The OpenAI model to use.

### `openai.config.temperature`

The randomness of the bot's responses. Higher temperature values result in more creative responses, but lower quality.

### `openai.botExternalName`

The name of the bot on Discord that users will see.

### `app.slowModeTime`

The minimum number of seconds that the bot will wait before responding to the same user again.

### `app.reactionWords`

A list of words that will trigger the bot to respond in the chat.

## Usage

To use the bot, simply run the command `php src/bot.php` in the project folder. The bot will connect to Discord and start listening to the chat. You can then start chatting with the bot and it will respond based on the settings you defined in the `config.override.yaml` file.

## Contributing

If you encounter any issues or have suggestions for improvement, please feel free to submit a pull request or open an issue on the GitHub repository.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
