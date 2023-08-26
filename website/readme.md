# Roblox Audio Fetcher (Website Edition) Way Easier

A simple web-based tool to fetch and play audio assets from Roblox.

## Usage

1. Clone or download this repository.

2. Open the `index.html` file in a web browser.

3. Enter the target Place ID and Asset ID in the input fields.

4. Click the "Fetch Audio" button.

5. The audio player will display the audio content if found.

## How It Works

- The `index.html` file provides a user interface for inputting the Place ID and Asset ID.
- When the "Fetch Audio" button is clicked, it sends a POST request to `audio.php` using the Fetch API.
- The `audio.php` file receives the Place ID and Asset ID through the POST request.
- It fetches the audio location using the provided Roblox cookie and asset details.
- If the audio location is found, it generates an HTML `<video>` element with the audio source.
- The `<video>` element is returned as a response to the Fetch API call and displayed in the audio player.

## Modifying the PHP Script

To use an alternate Roblox account (alt) with a different `.ROBLOSECURITY` cookie:

1. In the `audio.php` script, replace the placeholder `ROBLOX_COOKIE_HERE` with your alt's `.ROBLOSECURITY` cookie.

## Requirements

- Web browser (Google Chrome, Mozilla Firefox, etc.)

## Disclaimer

- Use this tool responsibly and with a secondary Roblox account to protect your primary account's security and privacy.
- Roblox may change their APIs or security measures, and this tool may not work in the future.

## Credits

This tool is inspired by the need to quickly fetch and play audio assets from Roblox.

## License

This project is licensed under the MIT License.
