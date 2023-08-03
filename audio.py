import tkinter as tk
from tkinter import filedialog
from tkinter import scrolledtext
import requests
import time
import re
import os

def fetch_audio_location(asset_id, place_id, roblox_cookie):
    while True:
        body_array = [{
            "assetId": asset_id,
            "assetType": "Audio",
            "requestId": "0"
        }]

        headers = {
            "User-Agent": "Roblox/WinInet",
            "Content-Type": "application/json",
            "Cookie": f".ROBLOSECURITY={roblox_cookie}",
            "Roblox-Place-Id": place_id,
            "Accept": "*/*",
            "Roblox-Browser-Asset-Request": "false"
        }

        response = requests.post('https://assetdelivery.roblox.com/v2/assets/batch', headers=headers, json=body_array)

        if response.status_code == 200:
            locations = response.json()

            if locations:
                obj = locations[0]
                if obj.get("locations") and obj["locations"][0].get("location"):
                    audio_url = obj["locations"][0]["location"]
                    return audio_url

        # Wait before retrying
        time.sleep(0.5)

def sanitize_filename(name):
    sanitized_name = re.sub(r'[\\/*?"<>|]', '', name)
    sanitized_name = sanitized_name.replace(" ", "_")  # Replace spaces with underscores
    return sanitized_name

def fetch_asset_name(asset_id):
    while True:
        response = requests.get(f"https://economy.roproxy.com/v2/assets/{asset_id}/details")

        if response.status_code == 200:
            asset_info = response.json()
            asset_name = asset_info.get("Name")
            if asset_name:
                return asset_name

        # Wait before retrying
        time.sleep(0.5)

def download_all_audio_files():
    roblox_cookie = roblox_cookie_entry.get()
    place_id = place_id_entry.get()

    file_path = filedialog.askopenfilename(filetypes=[("Text files", "*.txt")])

    if not file_path:
        return

    asset_ids = []
    with open(file_path, 'r') as file:
        for line in file:
            asset_ids.extend(line.strip().split(','))

    for asset_id in asset_ids:
        asset_name = fetch_asset_name(asset_id)  # Fetch the asset name here
        sanitized_asset_name = re.sub(r'[\\/*?"<>|]', '', asset_name)  # Remove disallowed characters

        audio_url = fetch_audio_location(asset_id, place_id, roblox_cookie)

        if audio_url:
            response = requests.get(audio_url)
            if response.status_code == 200:
                # Create a folder for downloaded audio files
                os.makedirs("audio_files", exist_ok=True)

                file_name = sanitized_asset_name
                file_path = os.path.join("audio_files", file_name + ".ogg")

                with open(file_path, "wb") as f:
                    f.write(response.content)
                print(f"Downloaded: {file_name}")

    # Display a popup message after downloading all assets
    tk.messagebox.showinfo("Download Complete", "All audio assets have been downloaded.")

    # Add ".ogg" extension to downloaded files
    for asset_id in asset_ids:
        asset_name = fetch_asset_name(asset_id)  # Fetch the asset name again
        old_path = os.path.join("audio_files", str(asset_name))
        new_path = os.path.join("audio_files", f"{asset_name}.ogg")
        os.rename(old_path, new_path)

# Create the main application window
app = tk.Tk()
app.title("Roblox Audio Fetcher")

# Create input fields and labels
place_id_label = tk.Label(app, text="Place ID:")
place_id_label.pack()
place_id_entry = tk.Entry(app)
place_id_entry.pack()

roblox_cookie_label = tk.Label(app, text="Roblox Cookie (.ROBLOSECURITY):")
roblox_cookie_label.pack()
roblox_cookie_entry = tk.Entry(app)
roblox_cookie_entry.pack()

load_button = tk.Button(app, text="Download All Audio Files", command=download_all_audio_files)
load_button.pack()

# Create a scrolled text area to display the result
result_text = scrolledtext.ScrolledText(app, height=10, width=60)
result_text.pack()

# Start the GUI event loop
app.mainloop()
