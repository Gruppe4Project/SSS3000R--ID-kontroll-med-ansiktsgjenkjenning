import cv2
import os
import mysql.connector
import qrcode
import smtplib
import ssl
import numpy as np
import time
import re  # ✅ Add this import to fix the error!
from datetime import datetime
from picamera2 import Picamera2
import tkinter as tk
from tkinter import ttk, messagebox
from PIL import Image, ImageTk
from email.message import EmailMessage
# ✅ Database Connection Details
DB_HOST = "localhost"
DB_USER = "group4"
DB_PASSWORD = "moh76med"
DB_NAME = "id_control"

# ✅ Email SMTP-server details (sett inn dine egne detaljer her før bruk)
EMAIL_SENDER = "din.epost@gmail.com"  # ← Legg inn din egen gmail e-postadresse
EMAIL_PASSWORD = "ditt_app_passord"        # ← Bruk Gmail-app-passord, ikke vanlig passord!
SMTP_SERVER = "smtp.gmail.com"
SMTP_PORT = 465

# ✅ Initialize Picamera2
picam2 = Picamera2()
picam2.configure(picam2.create_preview_configuration(main={"format": 'XRGB8888', "size": (640, 480)}))

picam2.start()

time.sleep(2)  # ✅ Allow camera to warm up

# ✅ Function to Create Necessary Folders
def create_folder(name):
    dataset_folder = "dataset"
    os.makedirs(dataset_folder, exist_ok=True)

    person_folder = os.path.join(dataset_folder, name)
    os.makedirs(person_folder, exist_ok=True)

    qr_folder = os.path.join(dataset_folder, "QR_codes")
    os.makedirs(qr_folder, exist_ok=True)

    return person_folder, qr_folder

# ✅ Function to Generate QR Code
def generate_qr(person_number):
    qr_folder = "dataset/QR_codes"
    os.makedirs(qr_folder, exist_ok=True)

    qr_filename = f"QR_{person_number}.png"
    qr_filepath = os.path.join(qr_folder, qr_filename)

    if os.path.exists(qr_filepath):
        return qr_filepath

    qr = qrcode.QRCode(version=1, box_size=10, border=5)
    qr.add_data(person_number)
    qr.make(fit=True)

    qr_img = qr.make_image(fill='black', back_color='white')
    qr_img.save(qr_filepath)

    return qr_filepath

# ✅ Function to Send QR Code via Email
def send_email(recipient_email, qr_filepath):
    if not os.path.exists(qr_filepath):
        return

    msg = EmailMessage()
    msg["Subject"] = "Your QR Code for Age Verification"
    msg["From"] = EMAIL_SENDER
    msg["To"] = recipient_email
    msg.set_content("Hello,\n\nYour unique QR code for age verification is attached.\n\nBest Regards,\nID-Control Team")

    with open(qr_filepath, "rb") as f:
        msg.add_attachment(f.read(), maintype="image", subtype="png", filename="QR-Code.png")

    try:
        context = ssl.create_default_context()
        with smtplib.SMTP_SSL(SMTP_SERVER, SMTP_PORT, context=context) as server:
            server.login(EMAIL_SENDER, EMAIL_PASSWORD)
            server.send_message(msg)
    except Exception as e:
        print(f"❌ Email sending error: {e}")

# ✅ Function to Insert or Update User in Database
def insert_or_update_user(fullname, person_number, dob, user_image, user_email, adresse):
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        cursor = conn.cursor()

        # ✅ Check if user exists
        check_query = "SELECT id, Qr_code FROM users WHERE personNumber = %s"
        cursor.execute(check_query, (person_number,))
        result = cursor.fetchone()

        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        qr_filepath = None  
        new_user = False  # ✅ Flag to check if it's a new user

        if result:
            user_id, existing_qr = result
            update_query = "UPDATE users SET user_image = %s, date_of_birth = %s, adresse = %s, created_at = %s WHERE id = %s"
            cursor.execute(update_query, (user_image, dob, adresse, timestamp, user_id))

            if not existing_qr:
                qr_filepath = generate_qr(person_number)
                update_qr_query = "UPDATE users SET Qr_code = %s WHERE id = %s"
                cursor.execute(update_qr_query, (qr_filepath, user_id))
        else:
            new_user = True  # ✅ Mark as a new user
            qr_filepath = generate_qr(person_number)
            insert_query = """
            INSERT INTO users (personNumber, fullname, date_of_birth, adresse, user_image, email, Qr_code, created_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(insert_query, (person_number, fullname, dob, adresse, user_image, user_email, qr_filepath, timestamp))

        conn.commit()
        cursor.close()
        conn.close()

        # ✅ Send QR Code via Email if it's a new user
        if new_user and qr_filepath:
            send_email(user_email, qr_filepath)

        # ✅ Show the correct success message
        if new_user:
            messagebox.showinfo("Success", f"User {fullname} Registered! QR Code Sent to {user_email}")
        else:
            messagebox.showinfo("Success", f"Image added and user {fullname} updated!")

    except mysql.connector.Error as err:
        print(f"❌ Database error: {err}")

# ✅ Function to Validate Date Format
def validate_date_format(date_text):
    if not date_text:  # ✅ Prevents validation if date is empty
        return False
    try:
        if not re.match(r'^\d{4}-\d{2}-\d{2}$', date_text):  # ✅ YYYY-MM-DD format check
            return False
        datetime.strptime(date_text, "%Y-%m-%d")  # ✅ Ensures valid date
        return True
    except ValueError:
        return False

# ✅ Function to Capture and Save Data
def capture_and_save():
    fullname = name_entry.get()
    person_number = person_number_entry.get()
    dob = dob_entry.get()
    email = email_entry.get()
    adresse = adresse_entry.get()

    # ✅ Ensure all fields are filled **before validating date**
    if not fullname or not person_number or not dob or not email or not adresse:
        messagebox.showerror("Error", "Please fill in all fields!")
        return

    # ✅ Validate person number length (Max 11)
    if len(person_number) > 11:
        messagebox.showerror("Error", "Person Number cannot be more than 11 characters!")
        return

    # ✅ Validate date format only if dob is not empty
    if not validate_date_format(dob):
        messagebox.showerror("Error", "Invalid date format! Use YYYY-MM-DD")
        return

    # ✅ Capture and Save Image
    person_folder, _ = create_folder(fullname)
    frame = picam2.capture_array()
    

    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"{fullname}_{timestamp}.jpg"
    filepath = os.path.join(person_folder, filename)
    cv2.imwrite(filepath, frame)

    # ✅ Insert into database
    insert_or_update_user(fullname, person_number, dob, filepath, email, adresse)

# ✅ Function to Update Camera Preview
def update_camera():
    frame = picam2.capture_array()
    frame = cv2.cvtColor(frame, cv2.COLOR_RGB2BGR)
    img = Image.fromarray(frame)
    img = img.resize((640, 480))
    img = ImageTk.PhotoImage(img)
    camera_label.imgtk = img
    camera_label.configure(image=img)
    root.after(10, update_camera)  # ✅ Keep updating

# ✅ Function to Close the Application
def close_application():
    picam2.stop()
    picam2.close()
    root.quit()
    root.destroy()

# ✅ GUI Initialization
root = tk.Tk()
root.title("User Registration")
root.geometry("900x850")
root.configure(bg="#F4F4F4")

# ✅ Modern Styled Theme with Hover Effect
style = ttk.Style()

# ✅ Green "Capture & Register" Button (Sharper Hover Effect)
style.configure("Green.TButton",
                background="#28A745",  # Green
                foreground="white",
                font=("Arial", 12, "bold"),
                padding=10,
                borderwidth=3,
                relief="ridge")  # Adds sharp border effect

style.map("Green.TButton",
          background=[("active", "#218838")],  # Darker Green on Hover
          foreground=[("active", "white")],
          relief=[("active", "solid")])  # Changes border on hover

# ✅ Red "Quit" Button (Sharper Hover Effect)
style.configure("Red.TButton",
                background="#DC3545",  # Red
                foreground="white",
                font=("Arial", 12, "bold"),
                padding=10,
                borderwidth=3,
                relief="ridge")  # Adds sharp border effect

style.map("Red.TButton",
          background=[("active", "#C82333")],  # Darker Red on Hover
          foreground=[("active", "white")],
          relief=[("active", "solid")])  # Changes border on hover

# ✅ Input Fields
fields = [("Full Name:", 50), ("Person Number:", 11), ("Date of Birth (YYYY-MM-DD):", 10), ("Email:", 50), ("Adresse:", 60)]
entries = []

for label_text, width in fields:
    frame = ttk.Frame(root)
    frame.pack(pady=5)
    label = ttk.Label(frame, text=label_text, font=("Arial", 12))
    label.pack(side="left", padx=10)
    entry = ttk.Entry(frame, width=width)
    entry.pack(side="right")
    entries.append(entry)

name_entry, person_number_entry, dob_entry, email_entry, adresse_entry = entries

# ✅ Camera Preview
camera_label = ttk.Label(root, background="black")
camera_label.pack(pady=10)

# ✅ Buttons
buttons_frame = ttk.Frame(root)
buttons_frame.pack(pady=20)

quit_button = ttk.Button(buttons_frame, text="Quit", command=close_application, style="Red.TButton", cursor="hand2")
quit_button.pack(side="left", padx=40, pady=10, ipadx=10, ipady=5)

capture_button = ttk.Button(buttons_frame, text="Capture & Register", command=capture_and_save, style="Green.TButton", cursor="hand2")
capture_button.pack(side="right", padx=40, pady=10, ipadx=10, ipady=5)


# ✅ Start Camera Preview
update_camera()

# ✅ Run GUI
root.mainloop()
