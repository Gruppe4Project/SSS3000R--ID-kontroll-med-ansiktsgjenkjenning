import subprocess
import sys
import tkinter as tk
from tkinter import ttk
from PIL import Image, ImageTk  # Import for better image handling

# ✅ Store running processes
processes = {}

# ✅ Function to run a script
def run_script(script_name):
    if script_name in processes and processes[script_name].poll() is None:
        status_label.config(text=f"⚠️ {script_name} is already running.", foreground="orange")
        return

    try:
        process = subprocess.Popen([sys.executable, script_name])
        processes[script_name] = process
        status_label.config(text=f"✅ Running {script_name}...", foreground="green")
    except Exception as e:
        status_label.config(text=f"❌ Error running {script_name}: {e}", foreground="red")

# ✅ Function to stop a script
def stop_script(script_name):
    if script_name in processes and processes[script_name].poll() is None:
        processes[script_name].terminate()
        processes[script_name].wait()
        status_label.config(text=f"🛑 Stopped {script_name}.", foreground="red")
    else:
        status_label.config(text=f"⚠️ {script_name} is not running.", foreground="orange")

# ✅ Close all scripts and exit
def exit_application():
    for script, process in processes.items():
        if process.poll() is None:
            process.terminate()
            process.wait()

    root.quit()
    root.destroy()

# ✅ Initialize GUI
root = tk.Tk()
root.title("ID-Kontroll Main Panel")
root.geometry("700x900")
root.configure(bg="#F4F4F4")


# ✅ Style Configuration
style = ttk.Style()
style.configure("TButton", font=("Arial", 12), padding=10, borderwidth=2)

# ✅ Define Different Colors for Each Task
style.configure("Green.TButton", background="#28A745", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("Green.TButton", background=[("active", "#218838")])

style.configure("Blue.TButton", background="#007BFF", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("Blue.TButton", background=[("active", "#0056b3")])

style.configure("Purple.TButton", background="#6F42C1", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("Purple.TButton", background=[("active", "#563d7c")])

style.configure("Orange.TButton", background="#FF8C00", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("Orange.TButton", background=[("active", "#E07B00")])

style.configure("Red.TButton", background="#DC3545", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("Red.TButton", background=[("active", "#C82333")])

style.configure("DarkGray.TButton", background="#343A40", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("DarkGray.TButton", background=[("active", "#23272B")])

style.configure("DarkRed.TButton", background="#8B0000", foreground="white", font=("Arial", 12, "bold"), relief="flat")
style.map("DarkRed.TButton", background=[("active", "#660000")])

# ✅ Title Label
title_label = ttk.Label(root, text="ID-KONTROLL MAIN OPPGAVE VALG", font=("Arial", 18, "bold"), background="#F4F4F4")
title_label.pack(pady=10)
# ✅ Add Logo Image (Top of GUI)
try:
    logo_image = Image.open("id_control_logo.png")  # Replace with your actual logo filename
    logo_image = logo_image.resize((150, 150), Image.Resampling.LANCZOS)  # Resize logo
    logo_photo = ImageTk.PhotoImage(logo_image)

    logo_label = tk.Label(root, image=logo_photo, background="#F4F4F4")
    logo_label.pack(pady=10)  # Adjust spacing around the logo

except Exception as e:
    print(f"Error loading logo: {e}")


# ✅ Sections and Buttons
sections = [
    ("Bruker_Registrering", "image_capture.py", "Green.TButton"),
    ("Modell_Trening", "model_training.py", "Blue.TButton"),
    ("Privat_Ansikt_Skanner", "facial_recognition.py", "Purple.TButton"),
    ("QR_Skanner", "qr_scanner.py", "Orange.TButton"),
    ("Nødetaten_Ansikt_Skanner", "face_rec_staten_model.py", "Red.TButton")
]

for idx, (section_name, script, btn_color) in enumerate(sections, start=1):
    # ✅ Section Title
    section_label = ttk.Label(root, text=section_name, font=("Arial", 14, "bold"), background="#BCDCDC", foreground="black", anchor="w")
    section_label.pack(pady=5, padx=30, fill="x")

    # ✅ Button Frame
    frame = ttk.Frame(root)
    frame.pack(pady=5, padx=30, fill="x")  # More spacing

    # ✅ Run Button
    run_btn = ttk.Button(frame, text=f"Start", command=lambda s=script: run_script(s), style=btn_color)
    run_btn.pack(side="left", expand=True, fill="x", padx=10, pady=5, ipadx=10, ipady=5)

    # ✅ Stop Button
    stop_btn = ttk.Button(frame, text=f"Stopp", command=lambda s=script: stop_script(s), style="DarkGray.TButton")
    stop_btn.pack(side="right", expand=True, fill="x", padx=10, pady=5, ipadx=10, ipady=5)

# ✅ Status Label
status_label = ttk.Label(root, text="", font=("Arial", 12), background="#F4F4F4")
status_label.pack(pady=10)

# ✅ Quit Button
quit_button = ttk.Button(root, text="Avslutt Programmet", command=exit_application, style="DarkRed.TButton")
quit_button.pack(pady=20, padx=20, fill="x")

# ✅ Run GUI
root.mainloop()
